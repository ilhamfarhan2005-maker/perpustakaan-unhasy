<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $librarian = User::where('role', 'pustakawan')->first()
            ?? User::where('role', 'admin')->firstOrFail();

        $students = User::where('role', 'mahasiswa')->get();

        if ($students->isEmpty()) {
            return;
        }


        // Ambil buku fisik atau fisik_digital via query dinamis (bukan ID tetap)
        $books = Book::whereIn('format', ['fisik', 'fisik_digital'])
            ->orderBy('id')
            ->get();

        if ($books->isEmpty()) {
            return;
        }

        // 25 transaksi dengan kombinasi status realistis:
        // 8 'dipinjam', 5 'terlambat', 8 'selesai', 4 'menungguKonfirmasiKembali'
        $scenarios = [
            // 8 Dipinjam
            ['days_ago' => 2, 'due_days' => 5, 'status' => 'dipinjam', 'workflow' => 'Dipinjam', 'returned' => null, 'req_return' => null],
            ['days_ago' => 3, 'due_days' => 4, 'status' => 'dipinjam', 'workflow' => 'Dipinjam', 'returned' => null, 'req_return' => null],
            ['days_ago' => 1, 'due_days' => 6, 'status' => 'dipinjam', 'workflow' => 'Dipinjam', 'returned' => null, 'req_return' => null],
            ['days_ago' => 4, 'due_days' => 3, 'status' => 'dipinjam', 'workflow' => 'Dipinjam', 'returned' => null, 'req_return' => null],
            ['days_ago' => 2, 'due_days' => 5, 'status' => 'dipinjam', 'workflow' => 'Dipinjam', 'returned' => null, 'req_return' => null],
            ['days_ago' => 5, 'due_days' => 2, 'status' => 'dipinjam', 'workflow' => 'Dipinjam', 'returned' => null, 'req_return' => null],
            ['days_ago' => 3, 'due_days' => 4, 'status' => 'dipinjam', 'workflow' => 'Dipinjam', 'returned' => null, 'req_return' => null],
            ['days_ago' => 1, 'due_days' => 6, 'status' => 'dipinjam', 'workflow' => 'Dipinjam', 'returned' => null, 'req_return' => null],

            // 5 Terlambat
            ['days_ago' => 14, 'due_days' => -7, 'status' => 'terlambat', 'workflow' => 'Dipinjam', 'returned' => null, 'req_return' => null],
            ['days_ago' => 12, 'due_days' => -5, 'status' => 'terlambat', 'workflow' => 'Dipinjam', 'returned' => null, 'req_return' => null],
            ['days_ago' => 10, 'due_days' => -3, 'status' => 'terlambat', 'workflow' => 'Dipinjam', 'returned' => null, 'req_return' => null],
            ['days_ago' => 15, 'due_days' => -8, 'status' => 'terlambat', 'workflow' => 'Dipinjam', 'returned' => null, 'req_return' => null],
            ['days_ago' => 11, 'due_days' => -4, 'status' => 'terlambat', 'workflow' => 'Dipinjam', 'returned' => null, 'req_return' => null],

            // 8 Selesai
            ['days_ago' => 25, 'due_days' => -18, 'status' => 'selesai', 'workflow' => 'Selesai', 'returned' => 18, 'req_return' => null],
            ['days_ago' => 24, 'due_days' => -17, 'status' => 'selesai', 'workflow' => 'Selesai', 'returned' => 17, 'req_return' => null],
            ['days_ago' => 22, 'due_days' => -15, 'status' => 'selesai', 'workflow' => 'Selesai', 'returned' => 16, 'req_return' => null],
            ['days_ago' => 20, 'due_days' => -13, 'status' => 'selesai', 'workflow' => 'Selesai', 'returned' => 13, 'req_return' => null],
            ['days_ago' => 18, 'due_days' => -11, 'status' => 'selesai', 'workflow' => 'Selesai', 'returned' => 12, 'req_return' => null],
            ['days_ago' => 16, 'due_days' => -9, 'status' => 'selesai', 'workflow' => 'Selesai', 'returned' => 9, 'req_return' => null],
            ['days_ago' => 15, 'due_days' => -8, 'status' => 'selesai', 'workflow' => 'Selesai', 'returned' => 8, 'req_return' => null],
            ['days_ago' => 14, 'due_days' => -7, 'status' => 'selesai', 'workflow' => 'Selesai', 'returned' => 7, 'req_return' => null],

            // 4 Menunggu Konfirmasi Kembali
            ['days_ago' => 6, 'due_days' => 1, 'status' => 'dipinjam', 'workflow' => 'Menunggu Konfirmasi Kembali', 'returned' => null, 'req_return' => true],
            ['days_ago' => 7, 'due_days' => 0, 'status' => 'dipinjam', 'workflow' => 'Menunggu Konfirmasi Kembali', 'returned' => null, 'req_return' => true],
            ['days_ago' => 8, 'due_days' => -1, 'status' => 'terlambat', 'workflow' => 'Menunggu Konfirmasi Kembali', 'returned' => null, 'req_return' => true],
            ['days_ago' => 5, 'due_days' => 2, 'status' => 'dipinjam', 'workflow' => 'Menunggu Konfirmasi Kembali', 'returned' => null, 'req_return' => true],
        ];

        foreach ($scenarios as $index => $item) {
            $student = $students[$index % $students->count()];
            $book = $books[$index % $books->count()];

            $borrowDate = today()->subDays($item['days_ago']);
            $dueDate = $item['due_days'] >= 0
                ? (clone $borrowDate)->addDays(7)
                : today()->subDays(abs($item['due_days']));
            $returnDate = $item['returned'] !== null
                ? today()->subDays($item['returned'])->toDateString()
                : null;
            $returnRequestedAt = $item['req_return'] ? now()->subHours($index + 1) : null;

            Transaction::firstOrCreate(
                [
                    'user_id' => $student->id,
                    'book_id' => $book->id,
                    'borrow_date' => $borrowDate->toDateString(),
                ],
                [
                    'handled_by' => $librarian->id,
                    'due_date' => $dueDate->toDateString(),
                    'return_date' => $returnDate,
                    'status' => $item['status'],
                    'extension_count' => $index % 3,
                    'workflow_status' => $item['workflow'],
                    'requested_at' => (clone $borrowDate)->subMinutes(30)->toDateTimeString(),
                    'return_requested_at' => $returnRequestedAt,
                    'approved_by' => $librarian->id,
                    'returned_confirmed_by' => $item['status'] === 'selesai' ? $librarian->id : null,
                ]
            );
        }
    }
}
