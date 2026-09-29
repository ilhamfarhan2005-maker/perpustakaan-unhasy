<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TransactionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        foreach (array_keys(Setting::KEYS) as $key) {
            Cache::forget("setting.{$key}");
        }

        parent::tearDown();
    }

    public function test_lending_extension_late_return_and_fine_payment_work_end_to_end(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-01 10:00:00'));
        Setting::set('borrow_duration_days', 7);
        Setting::set('max_extension', 2);
        Setting::set('max_borrow_per_user', 3);
        Setting::set('fine_per_day', 1000);

        $staff = $this->makeUser('Pustakawan', 'pustakawan', 'PST001');
        $member = $this->makeUser('Mahasiswa Demo', 'mahasiswa', '2023010001');
        $book = $this->makeBook(2);
        $this->actingAs($staff);

        $this->post(route('pustakawan.peminjaman.store'), [
            'nim_nip' => $member->nim_nip,
            'isbn' => $book->isbn,
        ])->assertRedirect();

        $transaction = Transaction::firstOrFail();
        $this->assertSame('dipinjam', $transaction->status);
        $this->assertSame('2026-09-08', $transaction->due_date->toDateString());
        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 1]);

        $this->post(route('pustakawan.perpanjangan.store', $transaction))
            ->assertSessionHas('success');
        $transaction->refresh();
        $this->assertSame(1, $transaction->extension_count);
        $this->assertSame('2026-09-15', $transaction->due_date->toDateString());

        Carbon::setTestNow(Carbon::parse('2026-09-18 10:00:00'));
        $this->post(route('pustakawan.pengembalian.store', $transaction))
            ->assertSessionHas('success');

        $transaction->refresh();
        $this->assertSame('selesai', $transaction->status);
        $this->assertSame('2026-09-18', $transaction->return_date->toDateString());
        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 2]);
        $this->assertDatabaseHas('fines', [
            'transaction_id' => $transaction->id,
            'days_late' => 3,
            'amount' => 3000,
            'status' => 'belum_lunas',
        ]);

        $fine = Fine::firstOrFail();
        $this->post(route('pustakawan.denda.bayar', $fine), ['paid_amount' => 3000])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('fines', ['id' => $fine->id, 'status' => 'lunas', 'paid_amount' => 3000]);

        $this->post(route('pustakawan.peminjaman.store'), [
            'nim_nip' => $member->nim_nip,
            'isbn' => $book->isbn,
        ])->assertRedirect();
        $this->assertSame(2, Transaction::count());
    }

    public function test_member_with_unpaid_fine_cannot_borrow(): void
    {
        $staff = $this->makeUser('Pustakawan', 'pustakawan', 'PST001');
        $member = $this->makeUser('Mahasiswa Demo', 'mahasiswa', '2023010001');
        $book = $this->makeBook(2);
        $transaction = Transaction::create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'handled_by' => $staff->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'selesai',
        ]);
        Fine::create([
            'transaction_id' => $transaction->id,
            'amount' => 5000,
            'days_late' => 5,
            'status' => 'belum_lunas',
        ]);

        $this->actingAs($staff)
            ->from(route('pustakawan.peminjaman.create'))
            ->post(route('pustakawan.peminjaman.store'), [
                'nim_nip' => $member->nim_nip,
                'isbn' => $book->isbn,
            ])
            ->assertRedirect(route('pustakawan.peminjaman.create'))
            ->assertSessionHas('error', 'Anggota masih memiliki denda yang belum dilunasi. Selesaikan dulu di menu Denda.');

        $this->assertSame(1, Transaction::count());
        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 2]);
    }

    public function test_extension_rules_and_transaction_pages(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-10 10:00:00'));
        Setting::set('max_extension', 2);
        $staff = $this->makeUser('Pustakawan', 'pustakawan', 'PST001');
        $member = $this->makeUser('Mahasiswa Demo', 'mahasiswa', '2023010001');
        $book = $this->makeBook(2);
        $this->actingAs($staff);

        $overdue = Transaction::create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'handled_by' => $staff->id,
            'borrow_date' => '2026-09-01',
            'due_date' => '2026-09-09',
            'status' => 'dipinjam',
        ]);
        $this->post(route('pustakawan.perpanjangan.store', $overdue))
            ->assertSessionHas('error', 'Tidak bisa diperpanjang karena sudah melewati jatuh tempo.');

        Carbon::setTestNow(Carbon::parse('2026-09-08 10:00:00'));
        $atLimit = Transaction::create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'handled_by' => $staff->id,
            'borrow_date' => '2026-09-01',
            'due_date' => '2026-09-10',
            'status' => 'dipinjam',
            'extension_count' => 2,
        ]);
        $this->post(route('pustakawan.perpanjangan.store', $atLimit))
            ->assertSessionHas('error', 'Sudah mencapai batas maksimal perpanjangan (2x).');

        $this->get(route('pustakawan.peminjaman.index'))->assertOk();
        $this->get(route('pustakawan.peminjaman.create'))->assertOk();
        $this->get(route('pustakawan.pengembalian.index'))->assertOk();
        $this->get(route('pustakawan.denda.index'))->assertOk();
    }

    private function makeUser(string $name, string $role, string $nimNip): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower($role).'-'.$nimNip.'@example.test',
            'password' => 'password',
            'role' => $role,
            'nim_nip' => $nimNip,
            'status' => 'active',
        ]);
    }

    private function makeBook(int $stock): Book
    {
        $category = Category::create(['name' => 'Umum', 'slug' => 'umum']);

        return Book::create([
            'category_id' => $category->id,
            'title' => 'Buku Uji Transaksi',
            'isbn' => '9780000000001',
            'author' => 'Penulis Uji',
            'publisher' => 'Penerbit Uji',
            'year' => 2024,
            'rak_location' => 'A-1',
            'stock' => $stock,
        ]);
    }
}
