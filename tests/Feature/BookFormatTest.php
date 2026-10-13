<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookFormatTest extends TestCase
{
    use RefreshDatabase;

    public function test_digital_book_can_be_saved_without_physical_stock_and_gets_inventory_code(): void
    {
        Storage::fake('public');
        $admin = $this->makeUser('Admin Buku', 'admin', 'ADM001');
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);

        $this->actingAs($admin)
            ->get(route('pustakawan.buku.create'))
            ->assertOk()
            ->assertSee('Format')
            ->assertSee('URL Pratinjau');

        $this->actingAs($admin)
            ->post(route('pustakawan.buku.store'), [
                'category_id' => $category->id,
                'title' => 'Buku Digital',
                'isbn' => '9780000000010',
                'author' => 'Penulis Uji',
                'publisher' => 'Penerbit Uji',
                'year' => 2025,
                'format' => 'digital',
                'preview_url' => 'https://example.org/pratinjau',
            ])
            ->assertRedirect(route('pustakawan.buku.index'));

        $book = Book::where('isbn', '9780000000010')->firstOrFail();
        $this->assertSame('UNHASY-BK-'.str_pad((string) $book->id, 6, '0', STR_PAD_LEFT), $book->kode_inventaris);
        $this->assertSame(0, $book->stock);
        $this->assertSame('-', $book->rak_location);
        $this->assertFalse($book->isAvailable());

        $this->get(route('pustakawan.buku.index'))
            ->assertOk()
            ->assertSee('Format')
            ->assertSee('Pratinjau');
        $this->get(route('pustakawan.buku.edit', $book))->assertOk();
        $this->get(route('pustakawan.buku.show', $book))
            ->assertOk()
            ->assertSee('Baca Pratinjau')
            ->assertDontSee('Stok');
    }

    public function test_digital_book_preview_url_requires_https(): void
    {
        $admin = $this->makeUser('Admin Buku', 'admin', 'ADM001');
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);

        $this->actingAs($admin)
            ->from(route('pustakawan.buku.create'))
            ->post(route('pustakawan.buku.store'), [
                'category_id' => $category->id,
                'title' => 'Buku Digital',
                'isbn' => '9780000000011',
                'author' => 'Penulis Uji',
                'publisher' => 'Penerbit Uji',
                'year' => 2025,
                'format' => 'digital',
                'preview_url' => 'javascript:alert(1)',
            ])
            ->assertSessionHasErrors('preview_url');

        $this->actingAs($admin)
            ->from(route('pustakawan.buku.create'))
            ->post(route('pustakawan.buku.store'), [
                'category_id' => $category->id,
                'title' => 'Buku Digital HTTP',
                'isbn' => '9780000000013',
                'author' => 'Penulis Uji',
                'publisher' => 'Penerbit Uji',
                'year' => 2025,
                'format' => 'digital',
                'preview_url' => 'http://example.org/pratinjau',
            ])
            ->assertSessionHasErrors('preview_url');
    }

    public function test_books_with_physical_stock_are_required_for_borrow_availability(): void
    {
        $admin = $this->makeUser('Admin Buku', 'admin', 'ADM001');
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);

        $this->actingAs($admin)
            ->from(route('pustakawan.buku.create'))
            ->post(route('pustakawan.buku.store'), [
                'category_id' => $category->id,
                'title' => 'Buku Fisik',
                'isbn' => '9780000000014',
                'author' => 'Penulis Uji',
                'publisher' => 'Penerbit Uji',
                'year' => 2025,
                'format' => 'fisik',
            ])
            ->assertSessionHasErrors(['rak_location', 'stock']);
    }

    public function test_available_book_statistics_ignore_digital_stock(): void
    {
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);

        Book::create([
            'category_id' => $category->id,
            'title' => 'Buku Fisik',
            'isbn' => '9780000000015',
            'author' => 'Penulis Uji',
            'publisher' => 'Penerbit Uji',
            'year' => 2025,
            'rak_location' => 'A-01',
            'stock' => 2,
            'format' => 'fisik',
        ]);

        Book::create([
            'category_id' => $category->id,
            'title' => 'Buku Digital',
            'isbn' => '9780000000016',
            'author' => 'Penulis Uji',
            'publisher' => 'Penerbit Uji',
            'year' => 2025,
            'rak_location' => '-',
            'stock' => 10,
            'format' => 'digital',
            'preview_url' => 'https://example.org/pratinjau',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertViewHas('availableBooks', 1);
    }

    public function test_student_cannot_borrow_digital_book_and_details_hide_stock(): void
    {
        $student = $this->makeUser('Mahasiswa Uji', 'mahasiswa', '2023010101');
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $book = Book::create([
            'category_id' => $category->id,
            'title' => 'Buku Digital',
            'isbn' => '9780000000012',
            'author' => 'Penulis Uji',
            'publisher' => 'Penerbit Uji',
            'year' => 2025,
            'rak_location' => '-',
            'stock' => 5,
            'format' => 'digital',
            'preview_url' => 'https://example.org/pratinjau',
        ]);

        $this->actingAs($student)
            ->post(route('mahasiswa.peminjaman.store', $book))
            ->assertSessionHasErrors('buku');

        $this->assertSame(0, Peminjaman::count());
        $this->get(route('katalog.show', $book))
            ->assertOk()
            ->assertSee('Digital')
            ->assertSee('Baca Pratinjau')
            ->assertDontSee('Stok')
            ->assertDontSee('Pinjam');
    }

    private function makeUser(string $name, string $role, string $nimNip): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)).'@example.org',
            'password' => 'password',
            'role' => $role,
            'nim_nip' => $nimNip,
            'status' => 'active',
        ]);
    }
}
