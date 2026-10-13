<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Peminjaman;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class StudentLoanRequestsTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_request_does_not_reduce_stock_until_staff_approves_it_and_can_request_return(): void
    {
        $student = $this->makeUser('Mahasiswa', 'mahasiswa');
        $staff = $this->makeUser('Pustakawan', 'pustakawan');
        $book = $this->makeBook();

        $this->actingAs($student)
            ->post(route('mahasiswa.peminjaman.store', $book))
            ->assertRedirect(route('katalog.show', $book))
            ->assertSessionHas('success');

        $borrowRequest = Peminjaman::firstOrFail();
        $this->assertSame('Diajukan', $borrowRequest->workflow_status);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 1]);

        $this->actingAs($staff)
            ->post(route('pustakawan.pengajuan.proses', $borrowRequest), ['action' => 'setujui'])
            ->assertSessionHas('success');

        $borrowRequest->refresh();
        $transaction = Transaction::findOrFail($borrowRequest->transaction_id);
        $this->assertSame('Dipinjam', $transaction->workflow_status);
        $this->assertSame('Dipinjam', $borrowRequest->workflow_status);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 0]);

        $this->actingAs($student)
            ->get(route('mahasiswa.riwayat'))
            ->assertOk()
            ->assertSee('Ajukan Pengembalian');

        $this->post(route('mahasiswa.pengembalian.store', $transaction))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->post(route('mahasiswa.pengembalian.store', $transaction))
            ->assertSessionHasErrors('pengajuan');

        $transaction->refresh();
        $borrowRequest->refresh();
        $this->assertSame('Menunggu Konfirmasi Kembali', $transaction->workflow_status);
        $this->assertNotNull($transaction->return_requested_at);
        $this->assertSame('Menunggu Konfirmasi Kembali', $borrowRequest->workflow_status);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 0]);

        $this->actingAs($staff)
            ->get(route('pustakawan.pengembalian.index'))
            ->assertOk()
            ->assertSee($book->title);

        $this->post(route('pustakawan.pengembalian.store', $transaction))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 1]);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'selesai',
            'workflow_status' => 'Selesai',
        ]);
        $this->assertDatabaseHas('peminjamans', [
            'id' => $borrowRequest->id,
            'status' => 'dikembalikan',
            'workflow_status' => 'Selesai',
        ]);
    }

    public function test_student_cannot_submit_duplicate_or_fined_borrow_requests(): void
    {
        $student = $this->makeUser('Mahasiswa', 'mahasiswa');
        $staff = $this->makeUser('Pustakawan', 'pustakawan');
        $book = $this->makeBook();

        $this->actingAs($student)
            ->post(route('mahasiswa.peminjaman.store', $book))
            ->assertSessionHas('success');

        $this->from(route('katalog.show', $book))
            ->post(route('mahasiswa.peminjaman.store', $book))
            ->assertRedirect(route('katalog.show', $book))
            ->assertSessionHasErrors('buku');
        $this->assertDatabaseCount('peminjamans', 1);

        $pastLoanBook = $this->makeBook('9780000000002');
        $transaction = Transaction::create([
            'user_id' => $student->id,
            'book_id' => $pastLoanBook->id,
            'handled_by' => $staff->id,
            'borrow_date' => today()->subDays(10),
            'due_date' => today()->subDays(5),
            'return_date' => today(),
            'status' => 'selesai',
        ]);
        Fine::create([
            'transaction_id' => $transaction->id,
            'user_id' => $student->id,
            'amount' => 5000,
            'days_late' => 5,
            'status' => 'belum_lunas',
        ]);

        $anotherBook = $this->makeBook('9780000000003');
        $this->post(route('mahasiswa.peminjaman.store', $anotherBook))
            ->assertSessionHasErrors('buku');

        $this->assertDatabaseCount('peminjamans', 1);
        $this->assertDatabaseHas('books', ['id' => $anotherBook->id, 'stock' => 1]);
    }

    public function test_student_return_request_is_limited_to_the_transaction_owner(): void
    {
        $student = $this->makeUser('Pemilik', 'mahasiswa');
        $otherStudent = $this->makeUser('Mahasiswa lain', 'mahasiswa');
        $staff = $this->makeUser('Pustakawan', 'pustakawan');
        $book = $this->makeBook();
        $transaction = Transaction::create([
            'user_id' => $student->id,
            'book_id' => $book->id,
            'handled_by' => $staff->id,
            'borrow_date' => today(),
            'due_date' => today()->addDays(7),
            'status' => 'dipinjam',
        ]);

        $this->actingAs($otherStudent)
            ->post(route('mahasiswa.pengembalian.store', $transaction))
            ->assertNotFound();

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'return_requested_at' => null,
        ]);
    }

    public function test_pending_request_expires_after_one_day_and_no_longer_reserves_stock(): void
    {
        $student = $this->makeUser('Mahasiswa', 'mahasiswa');
        $book = $this->makeBook();
        $this->actingAs($student)
            ->post(route('mahasiswa.peminjaman.store', $book))
            ->assertSessionHas('success');

        $request = Peminjaman::firstOrFail();
        $request->forceFill(['created_at' => now()->subDay()->subMinute()])->save();

        Artisan::call('peminjaman:expire-requests');

        $request->refresh();
        $this->assertSame('ditolak', $request->status);
        $this->assertSame('Kedaluwarsa', $request->workflow_status);

        $this->post(route('mahasiswa.peminjaman.store', $book))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('peminjamans', 2);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 1]);
    }

    public function test_staff_must_provide_a_reason_when_rejecting_a_request(): void
    {
        $student = $this->makeUser('Mahasiswa', 'mahasiswa');
        $staff = $this->makeUser('Pustakawan', 'pustakawan');
        $book = $this->makeBook();

        $this->actingAs($student)->post(route('mahasiswa.peminjaman.store', $book));
        $request = Peminjaman::firstOrFail();

        $this->actingAs($staff)
            ->post(route('pustakawan.pengajuan.proses', $request), ['action' => 'tolak'])
            ->assertSessionHasErrors('alasan_penolakan');

        $this->post(route('pustakawan.pengajuan.proses', $request), [
            'action' => 'tolak',
            'alasan_penolakan' => 'Eksemplar sedang diperlukan untuk inventaris.',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('peminjamans', [
            'id' => $request->id,
            'status' => 'ditolak',
            'rejection_reason' => 'Eksemplar sedang diperlukan untuk inventaris.',
        ]);
    }

    private function makeUser(string $name, string $role): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '-', $name)).'-'.$role.'-'.fake()->unique()->numerify('####').'@example.test',
            'password' => 'password',
            'role' => $role,
            'nim_nip' => strtoupper($role).fake()->unique()->numerify('####'),
            'status' => 'active',
        ]);
    }

    private function makeBook(string $isbn = '9780000000001'): Book
    {
        $category = Category::firstOrCreate(['slug' => 'umum'], ['name' => 'Umum']);

        return Book::create([
            'category_id' => $category->id,
            'title' => 'Buku Uji '.$isbn,
            'isbn' => $isbn,
            'author' => 'Penulis Uji',
            'publisher' => 'Penerbit Uji',
            'year' => 2025,
            'rak_location' => 'A-1',
            'stock' => 1,
        ]);
    }
}
