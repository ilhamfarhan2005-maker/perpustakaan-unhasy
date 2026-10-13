<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_create_update_or_delete_book(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        // Create
        $this->actingAs($student)
            ->post(route('pustakawan.buku.store'), [
                'category_id' => $category->id,
                'title' => 'Buku Baru',
                'isbn' => '9781112223334',
                'author' => 'Penulis',
                'publisher' => 'Penerbit',
                'year' => 2024,
                'format' => 'fisik',
                'rak_location' => 'A-01',
                'stock' => 5,
            ])
            ->assertForbidden();

        // Update
        $this->actingAs($student)
            ->put(route('pustakawan.buku.update', $book), [
                'category_id' => $category->id,
                'title' => 'Judul Diubah',
                'isbn' => $book->isbn,
                'author' => $book->author,
                'publisher' => $book->publisher,
                'year' => $book->year,
                'format' => $book->format,
                'rak_location' => $book->rak_location,
                'stock' => $book->stock,
            ])
            ->assertForbidden();

        // Delete
        $this->actingAs($student)
            ->delete(route('pustakawan.buku.destroy', $book))
            ->assertForbidden();
    }

    public function test_student_cannot_access_locked_original_digital_file(): void
    {
        Storage::fake('local');

        $student = User::factory()->mahasiswa()->create();
        $filePath = 'digital-books/sample.pdf';
        Storage::disk('local')->put($filePath, '%PDF-1.4 test');

        $book = Book::factory()->digital()->create([
            'file_path' => $filePath,
        ]);

        $this->actingAs($student)
            ->get(route('pustakawan.buku.file-asli', $book))
            ->assertForbidden();
    }

    public function test_librarian_and_admin_can_manage_books(): void
    {
        $librarian = User::factory()->pustakawan()->create();
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        $this->actingAs($librarian)
            ->get(route('pustakawan.buku.edit', $book))
            ->assertOk();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)
            ->get(route('pustakawan.buku.create'))
            ->assertOk();
    }
}

