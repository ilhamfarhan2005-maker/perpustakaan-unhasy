<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $borrowDate = today()->subDays(fake()->numberBetween(1, 5));
        $dueDate = (clone $borrowDate)->addDays(7);

        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'handled_by' => User::factory(),
            'borrow_date' => $borrowDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'return_date' => null,
            'status' => 'dipinjam',
            'extension_count' => 0,
            'workflow_status' => 'Dipinjam',
            'requested_at' => $borrowDate->toDateTimeString(),
            'return_requested_at' => null,
            'approved_by' => null,
            'returned_confirmed_by' => null,
        ];
    }

    public function dipinjam(): static
    {
        return $this->state(function (array $attributes) {
            $borrowDate = today()->subDays(2);
            return [
                'borrow_date' => $borrowDate->toDateString(),
                'due_date' => (clone $borrowDate)->addDays(7)->toDateString(),
                'return_date' => null,
                'status' => 'dipinjam',
                'workflow_status' => 'Dipinjam',
                'return_requested_at' => null,
            ];
        });
    }

    public function terlambat(): static
    {
        return $this->state(function (array $attributes) {
            $borrowDate = today()->subDays(12);
            $dueDate = today()->subDays(5);
            return [
                'borrow_date' => $borrowDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'return_date' => null,
                'status' => 'terlambat',
                'workflow_status' => 'Dipinjam',
                'return_requested_at' => null,
            ];
        });
    }

    public function selesai(): static
    {
        return $this->state(function (array $attributes) {
            $borrowDate = today()->subDays(14);
            $dueDate = today()->subDays(7);
            $returnDate = today()->subDays(6);
            return [
                'borrow_date' => $borrowDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'return_date' => $returnDate->toDateString(),
                'status' => 'selesai',
                'workflow_status' => 'Selesai',
                'return_requested_at' => null,
            ];
        });
    }

    public function menungguKonfirmasiKembali(): static
    {
        return $this->state(function (array $attributes) {
            $borrowDate = today()->subDays(4);
            return [
                'borrow_date' => $borrowDate->toDateString(),
                'due_date' => (clone $borrowDate)->addDays(7)->toDateString(),
                'return_date' => null,
                'status' => 'dipinjam',
                'workflow_status' => 'Menunggu Konfirmasi Kembali',
                'return_requested_at' => now(),
            ];
        });
    }
}
