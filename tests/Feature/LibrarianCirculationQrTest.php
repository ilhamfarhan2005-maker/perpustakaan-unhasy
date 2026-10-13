<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Peminjaman;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibrarianCirculationQrTest extends TestCase
{
    use RefreshDatabase;

    public function test_librarian_can_record_a_loan_using_inventory_code_and_view_member_and_book_cards(): void
    {
        Setting::set('borrow_duration_days', 9);
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST005');
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010050');
        $book = $this->makeBook(1);

        $this->actingAs($staff)
            ->get(route('pustakawan.peminjaman.identifikasi', ['nim_nip' => $student->nim_nip]))
            ->assertOk()
            ->assertJsonPath('member.name', $student->name)
            ->assertJsonPath('member.active_loans', 0)
            ->assertJsonPath('member.unpaid_fine', false);

        $this->get(route('pustakawan.peminjaman.identifikasi', ['isbn' => $book->kode_inventaris]))
            ->assertOk()
            ->assertJsonPath('book.title', $book->title)
            ->assertJsonPath('book.stock', 1)
            ->assertJsonPath('book.borrowable', true);

        $this->post(route('pustakawan.peminjaman.store'), [
            'nim_nip' => $student->nim_nip,
            'isbn' => $book->kode_inventaris,
        ])->assertRedirect();

        $transaction = Transaction::firstOrFail();
        $this->assertSame(today()->addDays(9)->toDateString(), $transaction->due_date->toDateString());
        $this->assertSame('Dipinjam', $transaction->workflow_status);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 0]);
    }

    public function test_zero_stock_book_cannot_be_borrowed(): void
    {
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST006');
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010051');
        $book = $this->makeBook(0);

        $this->actingAs($staff)
            ->from(route('pustakawan.peminjaman.create'))
            ->post(route('pustakawan.peminjaman.store'), [
                'nim_nip' => $student->nim_nip,
                'isbn' => $book->isbn,
            ])
            ->assertRedirect(route('pustakawan.peminjaman.create'))
            ->assertSessionHas('error', "Stok buku \"{$book->title}\" sedang habis.");

        $this->assertSame(0, Transaction::count());
    }

    public function test_librarian_can_approve_or_reject_student_borrow_requests(): void
    {
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST007');
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010052');
        $otherStudent = $this->makeUser('Mahasiswa Kedua', 'mahasiswa', '2023010053');
        $book = $this->makeBook(1);
        $approvedRequest = Peminjaman::create([
            'user_id' => $student->id,
            'buku_id' => $book->id,
            'tanggal_pinjam' => today(),
            'status' => 'pending',
        ]);
        $rejectedRequest = Peminjaman::create([
            'user_id' => $otherStudent->id,
            'buku_id' => $book->id,
            'tanggal_pinjam' => today(),
            'status' => 'pending',
        ]);

        $this->actingAs($staff)
            ->get(route('pustakawan.peminjaman.index'))
            ->assertOk()
            ->assertSee('Pengajuan dari Mahasiswa')
            ->assertSee($student->name);

        $this->post(route('pustakawan.pengajuan.proses', $approvedRequest), ['action' => 'setujui'])
            ->assertSessionHas('success');
        $approvedRequest->refresh();
        $this->assertSame('dipinjam', $approvedRequest->status);
        $this->assertNotNull($approvedRequest->transaction_id);
        $this->assertSame($staff->id, $approvedRequest->transaction->approved_by);
        $this->assertSame(0, $book->fresh()->stock);

        $this->post(route('pustakawan.pengajuan.proses', $rejectedRequest), [
            'action' => 'tolak',
            'alasan_penolakan' => 'Buku sedang diperlukan untuk inventaris.',
        ])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('peminjamans', ['id' => $rejectedRequest->id, 'status' => 'ditolak']);
        $this->assertSame(1, Transaction::count());
    }

    public function test_librarian_can_confirm_a_student_return_request(): void
    {
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST010');
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010056');
        $book = $this->makeBook(0);
        $transaction = Transaction::create([
            'user_id' => $student->id,
            'book_id' => $book->id,
            'handled_by' => $staff->id,
            'borrow_date' => today()->subDays(3),
            'due_date' => today()->addDays(4),
            'status' => 'dipinjam',
        ]);
        $returnRequest = Peminjaman::create([
            'user_id' => $student->id,
            'buku_id' => $book->id,
            'transaction_id' => $transaction->id,
            'tanggal_pinjam' => today()->subDays(3),
            'requested_return_at' => now(),
            'status' => 'dipinjam',
        ]);

        $this->actingAs($staff)
            ->post(route('pustakawan.pengajuan.proses', $returnRequest), ['action' => 'konfirmasi_kembali'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('peminjamans', [
            'id' => $returnRequest->id,
            'status' => 'dikembalikan',
        ]);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'workflow_status' => 'Selesai',
            'returned_confirmed_by' => $staff->id,
        ]);
        $this->assertSame(1, $book->fresh()->stock);
    }

    public function test_return_search_accepts_transaction_code_member_nim_and_book_code(): void
    {
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST008');
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010054');
        $book = $this->makeBook(0);
        $transaction = Transaction::create([
            'user_id' => $student->id,
            'book_id' => $book->id,
            'handled_by' => $staff->id,
            'borrow_date' => today()->subDays(3),
            'due_date' => today()->addDays(4),
            'status' => 'dipinjam',
        ]);

        $this->actingAs($staff);
        foreach (['TRX-'.str_pad((string) $transaction->id, 6, '0', STR_PAD_LEFT), $student->nim_nip, $book->kode_inventaris] as $search) {
            $this->get(route('pustakawan.pengembalian.index', ['search' => $search]))
                ->assertOk()
                ->assertSee($book->title)
                ->assertSee('Proses kembali');
        }
    }

    public function test_librarian_can_print_book_qr_labels_and_member_card_as_svg(): void
    {
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST009');
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010055');
        $book = $this->makeBook(2);

        $this->actingAs($staff)
            ->get(route('pustakawan.buku.label-qr', $book))
            ->assertOk()
            ->assertSee('data:image/svg+xml;base64,', false)
            ->assertSee($book->kode_inventaris);

        $this->post(route('pustakawan.buku.label-qr.batch'), ['books' => [$book->id]])
            ->assertOk()
            ->assertSee($book->kode_inventaris);

        $this->get(route('pustakawan.anggota.kartu-qr', $student))
            ->assertOk()
            ->assertSee('data:image/svg+xml;base64,', false)
            ->assertSee($student->nim_nip);
    }

    private function makeBook(int $stock): Book
    {
        $category = Category::firstOrCreate(['slug' => 'uji-sirkulasi'], ['name' => 'Uji Sirkulasi']);

        return Book::create([
            'category_id' => $category->id,
            'title' => 'Buku Sirkulasi',
            'isbn' => '978000'.str_pad((string) (Book::count() + 1), 7, '0', STR_PAD_LEFT),
            'author' => 'Penulis Uji',
            'publisher' => 'Penerbit Uji',
            'year' => 2025,
            'rak_location' => 'A-01',
            'stock' => $stock,
            'format' => 'fisik',
        ]);
    }

    private function makeUser(string $name, string $role, string $nimNip): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)).$nimNip.'@example.org',
            'password' => 'password',
            'role' => $role,
            'nim_nip' => $nimNip,
            'status' => 'active',
        ]);
    }
}
