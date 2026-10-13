<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\StoreFinePaymentRequest;
use App\Models\Fine;
use App\Models\FinePayment;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class DendaSayaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $fines = Fine::with(['transaction.book', 'payments' => fn ($query) => $query->latest()])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('transaction', fn ($transaction) => $transaction->where('user_id', $user->id));
            })
            ->latest()
            ->get();
        $availableFines = $fines->filter(function (Fine $fine) {
            return ! $fine->isLunas()
                && $fine->payment_status !== 'menunggu_verifikasi'
                && ! $fine->payments->contains(fn (FinePayment $payment) => $payment->status === 'menunggu');
        });
        $totalBelumDibayar = $fines->reject(fn (Fine $fine) => $fine->isLunas())->sum('amount');

        return view('mahasiswa.denda.index', [
            'fines' => $fines,
            'availableFines' => $availableFines,
            'totalBelumDibayar' => $totalBelumDibayar,
            'qrisImagePath' => Setting::get('qris_image_path'),
        ]);
    }

    public function storePayment(StoreFinePaymentRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $proofPath = null;

        DB::transaction(function () use ($request, $user, $data, &$proofPath) {
            $fines = Fine::whereIn('id', $data['fines'])
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhereHas('transaction', fn ($transaction) => $transaction->where('user_id', $user->id));
                })
                ->lockForUpdate()
                ->get();

            if ($fines->count() !== count(array_unique($data['fines']))) {
                throw ValidationException::withMessages([
                    'fines' => 'Satu atau beberapa denda tidak ditemukan atau bukan milik Anda.',
                ]);
            }

            foreach ($fines as $fine) {
                $hasPendingPayment = $fine->payments()->where('status', 'menunggu')->exists();
                if ($fine->isLunas() || $fine->payment_status === 'menunggu_verifikasi' || $hasPendingPayment) {
                    throw ValidationException::withMessages([
                        'fines' => 'Pilih hanya denda yang belum lunas dan tidak sedang menunggu verifikasi.',
                    ]);
                }
            }

            $proofPath = $request->file('proof')->store('fine-payments', 'local');
            if ($proofPath === false) {
                throw new RuntimeException('Bukti pembayaran gagal disimpan ke penyimpanan privat.');
            }

            $payment = FinePayment::create([
                'user_id' => $user->id,
                'method' => 'qris',
                'amount' => $fines->sum('amount'),
                'proof_path' => $proofPath,
                'status' => 'menunggu',
            ]);
            $payment->fines()->attach($fines->modelKeys());
            Fine::whereKey($fines->modelKeys())->update(['payment_status' => 'menunggu_verifikasi']);
        });

        return back()->with('success', 'Bukti pembayaran berhasil dikirim dan sedang menunggu verifikasi.');
    }
}
