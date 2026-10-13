<?php

namespace Database\Seeders;

use App\Models\Fine;
use App\Models\FinePayment;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class FineSeeder extends Seeder
{
    public function run(): void
    {
        $librarian = User::where('role', 'pustakawan')->first()
            ?? User::where('role', 'admin')->firstOrFail();

        $fineRate = (float) Setting::get('fine_per_day', 1000);

        // Ambil transaksi yang terlambat untuk dibuatkan denda
        $lateTransactions = Transaction::where('status', 'terlambat')
            ->orWhere(function ($query) {
                $query->where('status', 'selesai')
                    ->whereColumn('return_date', '>', 'due_date');
            })
            ->get();

        if ($lateTransactions->isEmpty()) {
            return;
        }

        $fines = [];
        foreach ($lateTransactions as $index => $trx) {
            $daysLate = $trx->return_date
                ? max(1, (int) $trx->due_date->diffInDays($trx->return_date, false))
                : max(1, (int) $trx->due_date->diffInDays(today(), false));

            $amount = $daysLate * $fineRate;

            $fine = Fine::firstOrCreate(
                ['transaction_id' => $trx->id],
                [
                    'user_id' => $trx->user_id,
                    'amount' => $amount,
                    'days_late' => $daysLate,
                    'status' => 'belum_lunas',
                    'payment_status' => 'belum_dibayar',
                ]
            );

            $fines[] = $fine;
        }

        // Variasikan status denda dan pembayaran:
        // 1. Denda dengan Pembayaran 'menunggu'
        if (isset($fines[0])) {
            $fine = $fines[0];
            $payment = FinePayment::firstOrCreate(
                [
                    'user_id' => $fine->user_id,
                    'amount' => $fine->amount,
                    'status' => 'menunggu',
                ],
                [
                    'method' => 'qris',
                    'proof_path' => null,
                    'verified_by' => null,
                    'verified_at' => null,
                    'rejection_note' => null,
                ]
            );

            if (! $payment->fines()->where('fines.id', $fine->id)->exists()) {
                $payment->fines()->attach($fine->id);
            }
            $fine->update(['payment_status' => 'menunggu_verifikasi']);
        }

        // 2. Denda dengan Pembayaran 'terverifikasi' (lunas)
        if (isset($fines[1])) {
            $fine = $fines[1];
            $payment = FinePayment::firstOrCreate(
                [
                    'user_id' => $fine->user_id,
                    'amount' => $fine->amount,
                    'status' => 'terverifikasi',
                ],
                [
                    'method' => 'qris',
                    'proof_path' => null,
                    'verified_by' => $librarian->id,
                    'verified_at' => now(),
                    'rejection_note' => null,
                ]
            );

            if (! $payment->fines()->where('fines.id', $fine->id)->exists()) {
                $payment->fines()->attach($fine->id);
            }
            $fine->update([
                'status' => 'lunas',
                'payment_status' => 'lunas',
                'paid_at' => now(),
                'paid_amount' => $fine->amount,
            ]);
        }

        // 3. Denda dengan Pembayaran 'ditolak'
        if (isset($fines[2])) {
            $fine = $fines[2];
            $payment = FinePayment::firstOrCreate(
                [
                    'user_id' => $fine->user_id,
                    'amount' => $fine->amount,
                    'status' => 'ditolak',
                ],
                [
                    'method' => 'qris',
                    'proof_path' => null,
                    'verified_by' => $librarian->id,
                    'verified_at' => now(),
                    'rejection_note' => 'Bukti transfer tidak jelas atau nominal tidak cocok.',
                ]
            );

            if (! $payment->fines()->where('fines.id', $fine->id)->exists()) {
                $payment->fines()->attach($fine->id);
            }
            $fine->update(['payment_status' => 'ditolak']);
        }

        // Sisanya dibiarkan 'belum_dibayar' (misal fines[3] dst)
    }
}
