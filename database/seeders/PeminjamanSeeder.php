<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Peminjaman;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role', 'mahasiswa')->orderBy('id')->get();
        $books = Book::whereIn('format', ['fisik', 'fisik_digital'])->orderBy('id')->get();

        if ($students->isEmpty() || $books->isEmpty()) {
            return;
        }

        // 1. Pengajuan Diajukan (Pending)
        Peminjaman::firstOrCreate(
            [
                'user_id' => $students[0]->id,
                'buku_id' => $books[0]->id,
                'status' => 'pending',
            ],
            [
                'tanggal_pinjam' => today()->toDateString(),
                'denda' => 0,
                'workflow_status' => 'Diajukan',
                'rejection_reason' => null,
            ]
        );

        // 2. Pengajuan Ditolak
        if (isset($students[1], $books[1])) {
            Peminjaman::firstOrCreate(
                [
                    'user_id' => $students[1]->id,
                    'buku_id' => $books[1]->id,
                    'status' => 'ditolak',
                    'workflow_status' => 'Ditolak',
                ],
                [
                    'tanggal_pinjam' => today()->subDays(3)->toDateString(),
                    'denda' => 0,
                    'rejection_reason' => 'Stok buku fisik sedang dalam proses perbaikan jilid.',
                ]
            );
        }

        // 3. Pengajuan Kedaluwarsa
        if (isset($students[2], $books[2])) {
            $expired = Peminjaman::firstOrCreate(
                [
                    'user_id' => $students[2]->id,
                    'buku_id' => $books[2]->id,
                    'workflow_status' => 'Kedaluwarsa',
                ],
                [
                    'tanggal_pinjam' => today()->subDays(2)->toDateString(),
                    'status' => 'ditolak',
                    'denda' => 0,
                    'rejection_reason' => 'Pengajuan kedaluwarsa karena tidak diambil lebih dari 24 jam.',
                ]
            );
            $expired->created_at = now()->subDays(2);
            $expired->saveQuietly();
        }

        // 4. Hubungkan pengajuan berstatus 'dipinjam' ke transaksi aktif bila ada
        $activeTransactions = Transaction::where('status', 'dipinjam')->take(3)->get();
        foreach ($activeTransactions as $trx) {
            Peminjaman::firstOrCreate(
                [
                    'transaction_id' => $trx->id,
                ],
                [
                    'user_id' => $trx->user_id,
                    'buku_id' => $trx->book_id,
                    'tanggal_pinjam' => $trx->borrow_date,
                    'status' => 'dipinjam',
                    'denda' => 0,
                    'workflow_status' => $trx->workflow_status,
                ]
            );
        }
    }
}
