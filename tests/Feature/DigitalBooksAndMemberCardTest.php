<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DigitalBooksAndMemberCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_digital_file_requires_a_real_pdf_and_original_is_not_available_to_students(): void
    {
        Storage::fake('local');
        $staff = $this->user('pustakawan');
        $student = $this->user('mahasiswa');
        $category = Category::create(['name' => 'Umum', 'slug' => 'umum']);

        $this->actingAs($staff)->post(route('pustakawan.buku.store'), [
            'category_id' => $category->id, 'title' => 'Digital', 'isbn' => '9781234567890', 'author' => 'A', 'publisher' => 'P', 'year' => 2025,
            'format' => 'digital', 'digital_file' => UploadedFile::fake()->createWithContent('buku.pdf', 'bukan pdf'),
        ])->assertSessionHasErrors('digital_file');

        $book = Book::create(['category_id' => $category->id, 'title' => 'PDF', 'isbn' => '9781234567891', 'author' => 'A', 'publisher' => 'P', 'year' => 2025, 'format' => 'digital', 'stock' => 0, 'rak_location' => '-', 'file_path' => 'digital-books/private.pdf']);
        Storage::disk('local')->put($book->file_path, '%PDF-test');
        $this->actingAs($student)->get(route('pustakawan.buku.file-asli', $book))->assertForbidden();
        $this->get(route('katalog.preview', $book))->assertNotFound();
        $this->actingAs($staff)->get(route('pustakawan.buku.file-asli', $book))->assertOk();
    }

    public function test_member_card_contains_logged_in_students_nim(): void
    {
        $student = $this->user('mahasiswa');
        $this->actingAs($student)->get(route('mahasiswa.kartu'))
            ->assertOk()->assertSee($student->nim_nip);
    }

    private function user(string $role): User
    {
        return User::create(['name' => ucfirst($role), 'email' => $role.'@example.test', 'password' => 'password', 'role' => $role, 'nim_nip' => $role === 'mahasiswa' ? '2026010010' : 'P001', 'status' => 'active']);
    }
}
