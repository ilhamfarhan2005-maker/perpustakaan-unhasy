<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class TransactionPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_librarian_and_admin_can_view_any_and_view_transactions(): void
    {
        $librarian = User::factory()->pustakawan()->create();
        $student = User::factory()->mahasiswa()->create();
        $book = Book::factory()->create();

        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'book_id' => $book->id,
            'handled_by' => $librarian->id,
        ]);

        $this->actingAs($librarian)
            ->get(route('pustakawan.peminjaman.index'))
            ->assertOk();

        $this->actingAs($librarian)
            ->get(route('pustakawan.peminjaman.show', $transaction))
            ->assertOk();
    }

    public function test_student_can_only_view_their_own_transaction(): void
    {
        $student1 = User::factory()->mahasiswa()->create();
        $student2 = User::factory()->mahasiswa()->create();
        $librarian = User::factory()->pustakawan()->create();
        $book = Book::factory()->create();

        $transaction = Transaction::factory()->create([
            'user_id' => $student1->id,
            'book_id' => $book->id,
            'handled_by' => $librarian->id,
        ]);

        // Student 1 can view own transaction via Gate
        $this->assertTrue(Gate::forUser($student1)->allows('view', $transaction));

        // Student 2 cannot view Student 1's transaction
        $this->assertFalse(Gate::forUser($student2)->allows('view', $transaction));

        // Student cannot viewAny transactions
        $this->assertFalse(Gate::forUser($student1)->allows('viewAny', Transaction::class));
    }

    public function test_no_one_can_delete_transactions(): void
    {
        $admin = User::factory()->admin()->create();
        $librarian = User::factory()->pustakawan()->create();
        $student = User::factory()->mahasiswa()->create();
        $book = Book::factory()->create();

        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'book_id' => $book->id,
            'handled_by' => $librarian->id,
        ]);

        $this->assertFalse(Gate::forUser($admin)->allows('delete', $transaction));
        $this->assertFalse(Gate::forUser($librarian)->allows('delete', $transaction));
        $this->assertFalse(Gate::forUser($student)->allows('delete', $transaction));
    }
}

