<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Fine;
use App\Models\FinePayment;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FinePaymentsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        foreach (array_keys(Setting::KEYS) as $key) {
            Cache::forget("setting.{$key}");
        }

        parent::tearDown();
    }

    public function test_student_can_submit_private_proof_and_staff_can_verify_payment(): void
    {
        Storage::fake('local');
        $student = $this->makeUser('Mahasiswa', 'mahasiswa');
        $staff = $this->makeUser('Pustakawan', 'pustakawan');
        $fine = $this->makeFine($student, $staff);

        $this->actingAs($student)
            ->post(route('mahasiswa.denda.pembayaran.store'), [
                'fines' => [$fine->id],
                'proof' => UploadedFile::fake()->image('bukti.png'),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $payment = FinePayment::firstOrFail();
        $this->assertSame('qris', $payment->method);
        $this->assertSame('menunggu', $payment->status);
        $this->assertSame((string) $fine->amount, $payment->amount);
        $this->assertStringStartsWith('fine-payments/', $payment->proof_path);
        Storage::disk('local')->assertExists($payment->proof_path);
        $this->assertDatabaseHas('fines', [
            'id' => $fine->id,
            'payment_status' => 'menunggu_verifikasi',
        ]);

        $this->actingAs($student)
            ->get(route('mahasiswa.pembayaran.bukti', $payment))
            ->assertOk();

        $this->actingAs($staff)
            ->get(route('pustakawan.pembayaran.bukti', $payment))
            ->assertOk();

        $this->actingAs($staff)
            ->post(route('pustakawan.denda.bayar', $fine), ['paid_amount' => 5000])
            ->assertSessionHas('error', 'Denda sedang menunggu verifikasi pembayaran QRIS.');

        $this->actingAs($staff)
            ->post(route('pustakawan.pembayaran.update', $payment), ['action' => 'setujui'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('fine_payments', [
            'id' => $payment->id,
            'status' => 'terverifikasi',
            'verified_by' => $staff->id,
        ]);
        $this->assertDatabaseHas('fines', [
            'id' => $fine->id,
            'status' => 'lunas',
            'payment_status' => 'lunas',
        ]);
    }

    public function test_one_payment_can_cover_multiple_fines(): void
    {
        Storage::fake('local');
        $student = $this->makeUser('Mahasiswa', 'mahasiswa');
        $staff = $this->makeUser('Pustakawan', 'pustakawan');
        $firstFine = $this->makeFine($student, $staff, '9780000000051');
        $secondFine = $this->makeFine($student, $staff, '9780000000052');

        $this->actingAs($student)
            ->post(route('mahasiswa.denda.pembayaran.store'), [
                'fines' => [$firstFine->id, $secondFine->id],
                'proof' => UploadedFile::fake()->image('bukti.png'),
            ])
            ->assertSessionHas('success');

        $payment = FinePayment::firstOrFail();
        $this->assertSame(10000.0, (float) $payment->amount);
        $this->assertCount(2, $payment->fines);
    }

    public function test_student_cannot_access_another_students_payment_proof(): void
    {
        Storage::fake('local');
        $owner = $this->makeUser('Pemilik', 'mahasiswa');
        $otherStudent = $this->makeUser('Mahasiswa lain', 'mahasiswa');
        $payment = FinePayment::create([
            'user_id' => $owner->id,
            'method' => 'qris',
            'amount' => 5000,
            'proof_path' => 'fine-payments/private.png',
            'status' => 'menunggu',
        ]);
        Storage::disk('local')->put($payment->proof_path, 'fake image');

        $this->actingAs($otherStudent)
            ->get(route('mahasiswa.pembayaran.bukti', $payment))
            ->assertForbidden();
    }

    public function test_payment_upload_rejects_non_images_and_files_over_two_megabytes(): void
    {
        $student = $this->makeUser('Mahasiswa', 'mahasiswa');
        $staff = $this->makeUser('Pustakawan', 'pustakawan');
        $fine = $this->makeFine($student, $staff);

        $this->actingAs($student)
            ->from(route('mahasiswa.denda.index'))
            ->post(route('mahasiswa.denda.pembayaran.store'), [
                'fines' => [$fine->id],
                'proof' => UploadedFile::fake()->create('bukti.jpg', 10, 'text/plain'),
            ])
            ->assertSessionHasErrors('proof');

        $this->post(route('mahasiswa.denda.pembayaran.store'), [
            'fines' => [$fine->id],
            'proof' => UploadedFile::fake()->image('besar.png')->size(2049),
        ])->assertSessionHasErrors('proof');

        $this->assertDatabaseCount('fine_payments', 0);
    }

    public function test_rejected_payment_can_be_submitted_again_and_requires_a_rejection_reason(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Setting::set('qris_image_path', 'qris/qris.png');
        $student = $this->makeUser('Mahasiswa', 'mahasiswa');
        $staff = $this->makeUser('Pustakawan', 'pustakawan');
        $fine = $this->makeFine($student, $staff);
        $payment = FinePayment::create([
            'user_id' => $student->id,
            'method' => 'qris',
            'amount' => 5000,
            'status' => 'menunggu',
        ]);
        $payment->fines()->attach($fine);

        $this->actingAs($staff)
            ->from(route('pustakawan.pembayaran.index'))
            ->post(route('pustakawan.pembayaran.update', $payment), ['action' => 'tolak'])
            ->assertSessionHasErrors('rejection_note');

        $this->post(route('pustakawan.pembayaran.update', $payment), [
            'action' => 'tolak',
            'rejection_note' => 'Bukti tidak terbaca.',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('fines', ['id' => $fine->id, 'payment_status' => 'ditolak']);
        $this->actingAs($student)
            ->get(route('mahasiswa.denda.index'))
            ->assertOk()
            ->assertSee('Bukti tidak terbaca.')
            ->assertSee('Kirim Bukti Pembayaran');

        $this->post(route('mahasiswa.denda.pembayaran.store'), [
            'fines' => [$fine->id],
            'proof' => UploadedFile::fake()->image('ulang.jpg'),
        ])->assertSessionHas('success');

        $this->assertDatabaseCount('fine_payments', 2);
    }

    public function test_admin_can_upload_public_qris_image(): void
    {
        Storage::fake('public');
        $admin = $this->makeUser('Admin', 'admin');

        $this->actingAs($admin)
            ->post(route('admin.pengaturan.update'), [
                'qris_image' => UploadedFile::fake()->image('qris.png'),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $path = Setting::get('qris_image_path');
        $this->assertNotEmpty($path);
        Storage::disk('public')->assertExists($path);
    }

    private function makeFine(User $student, User $staff, string $isbn = '9780000000051'): Fine
    {
        $category = Category::firstOrCreate(['slug' => 'umum'], ['name' => 'Umum']);
        $book = Book::create([
            'category_id' => $category->id,
            'title' => 'Buku denda',
            'isbn' => $isbn,
            'author' => 'Penulis Uji',
            'publisher' => 'Penerbit Uji',
            'year' => 2025,
            'rak_location' => 'A-1',
            'stock' => 1,
        ]);
        $transaction = Transaction::create([
            'user_id' => $student->id,
            'book_id' => $book->id,
            'handled_by' => $staff->id,
            'borrow_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->subDays(5)->toDateString(),
            'return_date' => now()->toDateString(),
            'status' => 'selesai',
        ]);

        return Fine::create([
            'transaction_id' => $transaction->id,
            'user_id' => $student->id,
            'amount' => 5000,
            'days_late' => 5,
            'status' => 'belum_lunas',
            'payment_status' => 'belum_dibayar',
        ]);
    }

    private function makeUser(string $name, string $role): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '-', $name)).'-'.$role.'@example.test',
            'password' => 'password',
            'role' => $role,
            'nim_nip' => strtoupper($role).random_int(1000, 9999),
            'status' => 'active',
        ]);
    }
}
