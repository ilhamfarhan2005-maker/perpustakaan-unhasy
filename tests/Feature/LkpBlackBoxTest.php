<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Peminjaman;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * LKP Pertemuan 7 — Laporan Pengujian Black-Box (24 test case).
 *
 * Anggota 1 (TC-01 s.d. TC-04b)  : CRUD Buku           — EP, BVA, CE
 * Anggota 2 (TC-05 s.d. TC-07b)  : Transaksi Peminjaman — EP, BVA, CE
 * Anggota 3 (TC-08 s.d. TC-10b)  : Status, Filter, Otorisasi — ST, AP, CE
 *
 * Nama method memuat ID test case agar mudah dipetakan ke laporan:
 *   php artisan test --filter=LkpBlackBoxTest --testdox
 */
class LkpBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    // ───────────────────────── ANGGOTA 1: CRUD BUKU ─────────────────────────

    /** Catatan: field 'format' otomatis default 'fisik' (prepareForValidation), sehingga rak dan stok ikut wajib. */
    public function test_tc01_ep_form_buku_kosong_ditolak(): void
    {
        $admin = $this->makeUser('Admin Uji', 'admin', 'ADM900');

        $this->actingAs($admin)
            ->post(route('pustakawan.buku.store'), [])
            ->assertSessionHasErrors(['category_id', 'title', 'isbn', 'author', 'publisher', 'year', 'rak_location', 'stock']);

        $this->assertSame(0, Book::count());
    }

    public function test_tc02_ep_form_buku_valid_tersimpan(): void
    {
        $admin = $this->makeUser('Admin Uji', 'admin', 'ADM901');

        $this->actingAs($admin)
            ->post(route('pustakawan.buku.store'), $this->bookPayload(['title' => 'Pemrograman Web Lanjut']))
            ->assertRedirect(route('pustakawan.buku.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('books', ['title' => 'Pemrograman Web Lanjut', 'stock' => 3]);
    }

    public function test_tc02b_bva_judul_tepat_255_karakter_tersimpan(): void
    {
        $admin = $this->makeUser('Admin Uji', 'admin', 'ADM902');
        $title = str_repeat('A', 255);

        $this->actingAs($admin)
            ->post(route('pustakawan.buku.store'), $this->bookPayload(['title' => $title]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('pustakawan.buku.index'));

        $this->assertDatabaseHas('books', ['title' => $title]);
    }

    public function test_tc02c_bva_judul_256_karakter_ditolak(): void
    {
        $admin = $this->makeUser('Admin Uji', 'admin', 'ADM903');

        $this->actingAs($admin)
            ->post(route('pustakawan.buku.store'), $this->bookPayload(['title' => str_repeat('A', 256)]))
            ->assertSessionHasErrors('title');

        $this->assertSame(0, Book::count());
    }

    public function test_tc03_ep_edit_buku_nilai_valid_terbarui(): void
    {
        $admin = $this->makeUser('Admin Uji', 'admin', 'ADM904');
        $book = $this->makeBook(2);

        $this->actingAs($admin)
            ->put(route('pustakawan.buku.update', $book), $this->bookPayload([
                'title' => 'Judul Baru',
                'isbn' => $book->isbn,
                'stock' => 7,
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('pustakawan.buku.index'));

        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => 'Judul Baru', 'stock' => 7]);
    }

    public function test_tc03b_ep_edit_judul_kosong_ditolak_data_lama_utuh(): void
    {
        $admin = $this->makeUser('Admin Uji', 'admin', 'ADM905');
        $book = $this->makeBook(2);
        $oldTitle = $book->title;

        $this->actingAs($admin)
            ->put(route('pustakawan.buku.update', $book), $this->bookPayload([
                'title' => '',
                'isbn' => $book->isbn,
            ]))
            ->assertSessionHasErrors('title');

        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => $oldTitle]);
    }

    public function test_tc04_ce_hapus_konfirmasi_ok_record_terhapus(): void
    {
        $admin = $this->makeUser('Admin Uji', 'admin', 'ADM906');
        $book = $this->makeBook(2);

        $this->actingAs($admin)
            ->delete(route('pustakawan.buku.destroy', $book))
            ->assertRedirect(route('pustakawan.buku.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    /**
     * Dialog konfirmasi adalah JavaScript di browser. Tes otomatis memastikan
     * tombol hapus dipasangi confirm() dan data tetap ada bila tidak ada request
     * DELETE (Batal). Klik "Batal" tetap perlu dicoba manual di browser.
     */
    public function test_tc04b_ce_hapus_batal_form_memakai_confirm_dan_record_tetap(): void
    {
        $admin = $this->makeUser('Admin Uji', 'admin', 'ADM907');
        $book = $this->makeBook(2);

        $this->actingAs($admin)
            ->get(route('pustakawan.buku.index'))
            ->assertOk()
            ->assertSee('return confirm(', false)
            ->assertSee($book->title);

        $this->assertDatabaseHas('books', ['id' => $book->id]);
    }

    // ───────────────────── ANGGOTA 2: TRANSAKSI PEMINJAMAN ─────────────────────

    public function test_tc05_ep_transaksi_kosong_ditolak(): void
    {
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST900');

        $this->actingAs($staff)
            ->post(route('pustakawan.peminjaman.store'), [])
            ->assertSessionHasErrors(['nim_nip', 'isbn']);

        $this->assertSame(0, Transaction::count());
    }

    public function test_tc05b_ep_transaksi_parsial_ditolak(): void
    {
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST901');
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010901');

        $this->actingAs($staff)
            ->post(route('pustakawan.peminjaman.store'), ['nim_nip' => $student->nim_nip])
            ->assertSessionHasErrors('isbn')
            ->assertSessionDoesntHaveErrors('nim_nip');

        $this->assertSame(0, Transaction::count());
    }

    public function test_tc06_ep_transaksi_valid_tersimpan_dan_stok_berkurang(): void
    {
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST902');
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010902');
        $book = $this->makeBook(5);

        $this->actingAs($staff)
            ->post(route('pustakawan.peminjaman.store'), [
                'nim_nip' => $student->nim_nip,
                'isbn' => $book->isbn,
            ])
            ->assertSessionHas('success');

        $this->assertSame(1, Transaction::count());
        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 4]);
    }

    public function test_tc06b_bva_stok_tepat_1_berhasil_menjadi_0(): void
    {
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST903');
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010903');
        $book = $this->makeBook(1);

        $this->actingAs($staff)
            ->post(route('pustakawan.peminjaman.store'), [
                'nim_nip' => $student->nim_nip,
                'isbn' => $book->isbn,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 0]);
    }

    public function test_tc06c_bva_stok_0_ditolak(): void
    {
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST904');
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010904');
        $book = $this->makeBook(0);

        $this->actingAs($staff)
            ->post(route('pustakawan.peminjaman.store'), [
                'nim_nip' => $student->nim_nip,
                'isbn' => $book->isbn,
            ])
            ->assertSessionHas('error');

        $this->assertSame(0, Transaction::count());
        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 0]);
    }

    public function test_tc07_ce_integritas_foreign_key_transaksi(): void
    {
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST905');
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010905');
        $book = $this->makeBook(3);

        $this->actingAs($staff)->post(route('pustakawan.peminjaman.store'), [
            'nim_nip' => $student->nim_nip,
            'isbn' => $book->isbn,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $student->id,
            'book_id' => $book->id,
            'handled_by' => $staff->id,
            'status' => 'dipinjam',
        ]);
    }

    public function test_tc07b_ce_isbn_tidak_ada_ditolak_tanpa_record_baru(): void
    {
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST906');
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010906');

        $this->actingAs($staff)
            ->post(route('pustakawan.peminjaman.store'), [
                'nim_nip' => $student->nim_nip,
                'isbn' => '9999',
            ])
            ->assertSessionHas('error');

        $this->assertSame(0, Transaction::count());
    }

    // ─────────────── ANGGOTA 3: STATUS, FILTER, OTORISASI ───────────────

    public function test_tc08_st_diajukan_ke_dipinjam(): void
    {
        [$staff, $student, $book] = $this->requestScenario();

        $this->actingAs($student)->post(route('mahasiswa.peminjaman.store', $book))->assertSessionHas('success');
        $request = Peminjaman::firstOrFail();
        $this->assertSame('Diajukan', $request->workflow_status);

        $this->actingAs($staff)
            ->post(route('pustakawan.pengajuan.proses', $request), ['action' => 'setujui'])
            ->assertSessionHas('success');

        $request->refresh();
        $this->assertSame('Dipinjam', $request->workflow_status);
        $this->assertSame('Dipinjam', Transaction::findOrFail($request->transaction_id)->workflow_status);
    }

    public function test_tc08b_st_dipinjam_ke_menunggu_kembali_ke_selesai(): void
    {
        [$staff, $student, $book] = $this->requestScenario();
        $transaction = $this->borrowedTransaction($staff, $student, $book);

        $this->actingAs($student)
            ->post(route('mahasiswa.pengembalian.store', $transaction))
            ->assertSessionHas('success');
        $this->assertSame('Menunggu Konfirmasi Kembali', $transaction->fresh()->workflow_status);

        $this->actingAs($staff)
            ->post(route('pustakawan.pengembalian.store', $transaction))
            ->assertSessionHas('success');

        $transaction->refresh();
        $this->assertSame('selesai', $transaction->status);
        $this->assertSame('Selesai', $transaction->workflow_status);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 2]);
    }

    public function test_tc08c_st_transisi_tidak_valid_ditolak(): void
    {
        [$staff, $student, $book] = $this->requestScenario();
        $transaction = $this->borrowedTransaction($staff, $student, $book);

        $this->actingAs($staff)->post(route('pustakawan.pengembalian.store', $transaction));
        $this->assertSame('Selesai', $transaction->fresh()->workflow_status);

        // Selesai -> mengajukan pengembalian lagi (dilarang)
        $this->actingAs($student)
            ->post(route('mahasiswa.pengembalian.store', $transaction))
            ->assertSessionHasErrors('pengajuan');

        // Pengajuan yang sudah disetujui tidak bisa disetujui ulang
        $request = Peminjaman::where('transaction_id', $transaction->id)->first();
        if ($request) {
            $this->actingAs($staff)
                ->post(route('pustakawan.pengajuan.proses', $request), ['action' => 'setujui'])
                ->assertSessionHas('error', 'Pengajuan ini sudah diproses.');
        }

        $this->assertSame('Selesai', $transaction->fresh()->workflow_status);
    }

    public function test_tc09_ap_filter_kategori_saja(): void
    {
        $this->catalogFixture();

        $this->get(route('katalog.index', ['kategori' => 'kategori-x']))
            ->assertOk()
            ->assertSee('Buku Alpha')->assertSee('Buku Beta')
            ->assertDontSee('Buku Gamma')->assertDontSee('Buku Delta');
    }

    public function test_tc09b_ap_filter_tersedia_saja(): void
    {
        $this->catalogFixture();

        $this->get(route('katalog.index', ['tersedia' => '1']))
            ->assertOk()
            ->assertSee('Buku Alpha')->assertSee('Buku Gamma')
            ->assertDontSee('Buku Beta')->assertDontSee('Buku Delta');
    }

    public function test_tc09c_ap_filter_kategori_dan_tersedia(): void
    {
        $this->catalogFixture();

        $this->get(route('katalog.index', ['kategori' => 'kategori-x', 'tersedia' => '1']))
            ->assertOk()
            ->assertSee('Buku Alpha')
            ->assertDontSee('Buku Beta')->assertDontSee('Buku Gamma')->assertDontSee('Buku Delta');
    }

    public function test_tc09d_ap_filter_kosong_tampil_semua(): void
    {
        $this->catalogFixture();

        $this->get(route('katalog.index'))
            ->assertOk()
            ->assertSee('Buku Alpha')->assertSee('Buku Beta')
            ->assertSee('Buku Gamma')->assertSee('Buku Delta');
    }

    public function test_tc10_ce_mahasiswa_mengakses_halaman_staf_dan_admin_ditolak_403(): void
    {
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010910');

        $this->actingAs($student)->get(route('pustakawan.dashboard'))->assertForbidden();
        $this->actingAs($student)->get(route('pustakawan.peminjaman.create'))->assertForbidden();
        $this->actingAs($student)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($student)->get(route('admin.pengguna.index'))->assertForbidden();
    }

    public function test_tc10b_ce_guest_diarahkan_ke_login(): void
    {
        $this->get(route('pustakawan.dashboard'))->assertRedirect(route('login'));
        $this->get(route('mahasiswa.riwayat'))->assertRedirect(route('login'));
        $this->get(route('admin.pengguna.index'))->assertRedirect(route('login'));
    }

    // ───────────────────────────── HELPERS ─────────────────────────────

    private function requestScenario(): array
    {
        $staff = $this->makeUser('Pustakawan Uji', 'pustakawan', 'PST950');
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010950');
        $book = $this->makeBook(2);

        return [$staff, $student, $book];
    }

    private function borrowedTransaction(User $staff, User $student, Book $book): Transaction
    {
        $this->actingAs($student)->post(route('mahasiswa.peminjaman.store', $book));
        $request = Peminjaman::firstOrFail();
        $this->actingAs($staff)->post(route('pustakawan.pengajuan.proses', $request), ['action' => 'setujui']);

        return Transaction::findOrFail($request->fresh()->transaction_id);
    }

    private function catalogFixture(): void
    {
        $x = Category::create(['name' => 'Kategori X', 'slug' => 'kategori-x']);
        $y = Category::create(['name' => 'Kategori Y', 'slug' => 'kategori-y']);

        $this->makeBook(2, 'Buku Alpha', $x);                // X, tersedia
        $this->makeBook(0, 'Buku Beta', $x);                 // X, stok habis
        $this->makeBook(3, 'Buku Gamma', $y);                // Y, tersedia
        $this->makeBook(0, 'Buku Delta', $y, 'digital');     // Y, digital
    }

    private function bookPayload(array $override = []): array
    {
        $category = Category::firstOrCreate(['slug' => 'umum'], ['name' => 'Umum']);

        return array_merge([
            'category_id' => $category->id,
            'title' => 'Judul Uji',
            'isbn' => '978'.random_int(1000000, 9999999),
            'author' => 'Penulis Uji',
            'publisher' => 'Penerbit Uji',
            'year' => 2024,
            'format' => 'fisik',
            'rak_location' => 'A-01',
            'stock' => 3,
        ], $override);
    }

    private function makeBook(int $stock, ?string $title = null, ?Category $category = null, string $format = 'fisik'): Book
    {
        $category ??= Category::firstOrCreate(['slug' => 'umum'], ['name' => 'Umum']);

        return Book::create([
            'category_id' => $category->id,
            'title' => $title ?? 'Buku Uji '.(Book::count() + 1),
            'isbn' => '978100'.str_pad((string) (Book::count() + 1), 7, '0', STR_PAD_LEFT),
            'author' => 'Penulis Uji',
            'publisher' => 'Penerbit Uji',
            'year' => 2025,
            'rak_location' => $format === 'digital' ? '-' : 'A-01',
            'stock' => $format === 'digital' ? 0 : $stock,
            'format' => $format,
            'preview_url' => $format === 'digital' ? 'https://example.org/preview' : null,
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
