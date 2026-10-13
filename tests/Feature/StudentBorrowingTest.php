<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentBorrowingTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_a_borrowing_request_for_an_available_book(): void
    {
        $student = $this->makeStudent();
        $book = $this->makeBook(1);

        $this->actingAs($student)
            ->post(route('mahasiswa.peminjaman.store', $book))
            ->assertRedirect(route('katalog.show', $book))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('peminjamans', [
            'user_id' => $student->id,
            'buku_id' => $book->id,
            'status' => 'pending',
            'denda' => 0,
        ]);
        $this->assertInstanceOf(User::class, Peminjaman::firstOrFail()->user);
        $this->assertInstanceOf(Book::class, Peminjaman::firstOrFail()->buku);
    }

    public function test_student_cannot_request_an_unavailable_book(): void
    {
        $student = $this->makeStudent();
        $book = $this->makeBook(0);

        $this->actingAs($student)
            ->from(route('katalog.show', $book))
            ->post(route('mahasiswa.peminjaman.store', $book))
            ->assertRedirect(route('katalog.show', $book))
            ->assertSessionHasErrors('buku');

        $this->assertDatabaseCount('peminjamans', 0);
    }

    public function test_student_can_see_borrow_action_and_request_on_dashboard_and_history(): void
    {
        $student = $this->makeStudent();
        $book = $this->makeBook(1);
        $this->actingAs($student);

        $this->get(route('katalog.show', $book))
            ->assertOk()
            ->assertSee('Ajukan Peminjaman');

        $this->post(route('mahasiswa.peminjaman.store', $book));
        Peminjaman::firstOrFail()->update(['denda' => 2000]);

        $this->get(route('mahasiswa.dashboard'))
            ->assertOk()
            ->assertSee('Peminjaman Aktif')
            ->assertSee($book->title)
            ->assertSee('Diajukan')
            ->assertSee('Rp 2.000');

        $this->get(route('mahasiswa.riwayat'))
            ->assertOk()
            ->assertSee($book->title)
            ->assertSee('Diajukan');
    }

    private function makeStudent(): User
    {
        return User::create([
            'name' => 'Mahasiswa Uji',
            'email' => 'mahasiswa@example.test',
            'password' => 'password',
            'role' => 'mahasiswa',
            'nim_nip' => '2026010001',
            'status' => 'active',
        ]);
    }

    private function makeBook(int $stock): Book
    {
        $category = Category::create(['name' => 'Umum', 'slug' => 'umum']);

        return Book::create([
            'category_id' => $category->id,
            'title' => 'Buku Uji',
            'isbn' => '9780000000001',
            'author' => 'Penulis Uji',
            'publisher' => 'Penerbit Uji',
            'year' => 2025,
            'rak_location' => 'A-1',
            'stock' => $stock,
        ]);
    }
}
