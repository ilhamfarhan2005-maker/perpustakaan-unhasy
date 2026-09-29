<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiService
{
    public function cekBolehPinjam(User $user): array
    {
        if ($user->status !== 'active') {
            return [false, 'Akun anggota tidak aktif. Hubungi admin.'];
        }

        if ($user->role !== 'mahasiswa') {
            return [false, 'Hanya mahasiswa yang dapat meminjam buku.'];
        }

        $hasUnpaidFine = Fine::whereHas('transaction', fn ($query) => $query->where('user_id', $user->id))
            ->where('status', 'belum_lunas')
            ->exists();

        if ($hasUnpaidFine) {
            return [false, 'Anggota masih memiliki denda yang belum dilunasi. Selesaikan dulu di menu Denda.'];
        }

        $maxBorrow = (int) Setting::get('max_borrow_per_user', 3);
        $activeBorrowCount = Transaction::where('user_id', $user->id)
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->count();

        if ($activeBorrowCount >= $maxBorrow) {
            return [false, "Anggota sudah meminjam {$activeBorrowCount} buku (batas maksimal {$maxBorrow})."];
        }

        return [true, ''];
    }

    public function hitungDenda(Transaction $transaksi, Carbon $tanggalKembali): array
    {
        $dueDate = Carbon::parse($transaksi->due_date)->startOfDay();
        $returnDate = $tanggalKembali->copy()->startOfDay();

        if ($returnDate->lte($dueDate)) {
            return [0, 0];
        }

        $daysLate = (int) $dueDate->diffInDays($returnDate);
        $amount = $daysLate * (float) Setting::get('fine_per_day', 1000);

        return [$daysLate, $amount];
    }

    public function buatPeminjaman(User $user, Book $book, User $petugas): Transaction
    {
        return DB::transaction(function () use ($user, $book, $petugas) {
            $user = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $book = Book::whereKey($book->id)->lockForUpdate()->firstOrFail();

            [$boleh, $alasan] = $this->cekBolehPinjam($user);
            if (! $boleh) {
                throw ValidationException::withMessages(['nim_nip' => $alasan]);
            }

            if ($book->stock < 1) {
                throw ValidationException::withMessages(['isbn' => "Stok buku \"{$book->title}\" sedang habis."]);
            }

            $borrowDate = now()->startOfDay();
            $duration = (int) Setting::get('borrow_duration_days', 7);

            $transaksi = Transaction::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'handled_by' => $petugas->id,
                'borrow_date' => $borrowDate,
                'due_date' => $borrowDate->copy()->addDays($duration),
                'status' => 'dipinjam',
            ]);

            $book->decrement('stock');

            return $transaksi;
        });
    }

    public function prosesPengembalian(Transaction $transaksi): array
    {
        return DB::transaction(function () use ($transaksi) {
            $transaksi = Transaction::whereKey($transaksi->id)->lockForUpdate()->firstOrFail();
            if (! in_array($transaksi->status, ['dipinjam', 'terlambat'], true)) {
                throw ValidationException::withMessages(['transaction' => 'Transaksi ini sudah selesai.']);
            }

            $today = now()->startOfDay();
            $transaksi->update(['return_date' => $today, 'status' => 'selesai']);
            $book = Book::whereKey($transaksi->book_id)->lockForUpdate()->firstOrFail();
            $book->increment('stock');

            [$daysLate, $amount] = $this->hitungDenda($transaksi, $today);
            $fine = null;
            if ($daysLate > 0) {
                $fine = Fine::create([
                    'transaction_id' => $transaksi->id,
                    'amount' => $amount,
                    'days_late' => $daysLate,
                    'status' => 'belum_lunas',
                ]);
            }

            return [
                'transaction' => $transaksi->fresh(),
                'days_late' => $daysLate,
                'amount' => $amount,
                'fine' => $fine,
            ];
        });
    }

    public function perpanjang(Transaction $transaksi): array
    {
        return DB::transaction(function () use ($transaksi) {
            $transaksi = Transaction::whereKey($transaksi->id)->lockForUpdate()->firstOrFail();
            $maxExtensions = (int) Setting::get('max_extension', 2);

            if ($transaksi->status !== 'dipinjam') {
                return [false, 'Hanya transaksi berstatus Dipinjam yang bisa diperpanjang.'];
            }

            if ($transaksi->extension_count >= $maxExtensions) {
                return [false, "Sudah mencapai batas maksimal perpanjangan ({$maxExtensions}x)."];
            }

            if (now()->startOfDay()->gt($transaksi->due_date)) {
                return [false, 'Tidak bisa diperpanjang karena sudah melewati jatuh tempo.'];
            }

            $duration = (int) Setting::get('borrow_duration_days', 7);
            $transaksi->update([
                'due_date' => $transaksi->due_date->copy()->addDays($duration),
                'extension_count' => $transaksi->extension_count + 1,
            ]);

            return [true, $transaksi->fresh()];
        });
    }

    public function bayarDenda(Fine $fine, float $paidAmount): Fine
    {
        return DB::transaction(function () use ($fine, $paidAmount) {
            $fine = Fine::whereKey($fine->id)->lockForUpdate()->firstOrFail();

            if ($fine->status === 'lunas') {
                throw ValidationException::withMessages(['fine' => 'Denda ini sudah lunas sebelumnya.']);
            }

            if ($paidAmount < (float) $fine->amount) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Jumlah bayar minimal Rp '.number_format($fine->amount, 0, ',', '.'),
                ]);
            }

            $fine->update(['paid_at' => now(), 'paid_amount' => $paidAmount, 'status' => 'lunas']);

            return $fine->fresh();
        });
    }
}