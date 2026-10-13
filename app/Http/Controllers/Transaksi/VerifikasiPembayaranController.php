<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\VerifyFinePaymentRequest;
use App\Models\Fine;
use App\Models\FinePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VerifikasiPembayaranController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', FinePayment::class);

        $payments = FinePayment::with(['user', 'fines.transaction.book'])
            ->where('status', 'menunggu')
            ->oldest()
            ->paginate(15);

        return view('transaksi.pembayaran.index', compact('payments'));
    }

    public function update(VerifyFinePaymentRequest $request, FinePayment $payment)
    {
        $this->authorize('verify', $payment);

        $data = $request->validated();
        DB::transaction(function () use ($request, $payment, $data) {
            $payment = FinePayment::whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($payment->status !== 'menunggu') {
                throw ValidationException::withMessages([
                    'payment' => 'Pembayaran ini sudah diproses sebelumnya.',
                ]);
            }

            $fines = Fine::whereIn('id', $payment->fines()->pluck('fines.id'))
                ->lockForUpdate()
                ->get();

            if ($fines->isEmpty()) {
                throw ValidationException::withMessages([
                    'payment' => 'Pembayaran ini tidak memiliki denda yang dapat diproses.',
                ]);
            }

            $approved = $data['action'] === 'setujui';
            if ($approved && ($fines->contains(fn (Fine $fine) => $fine->isLunas())
                || round((float) $fines->sum('amount'), 2) !== round((float) $payment->amount, 2))) {
                throw ValidationException::withMessages([
                    'payment' => 'Nominal pembayaran tidak sesuai dengan denda yang terkait.',
                ]);
            }

            $payment->update([
                'status' => $approved ? 'terverifikasi' : 'ditolak',
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
                'rejection_note' => $approved ? null : $data['rejection_note'],
            ]);

            foreach ($fines as $fine) {
                if ($approved) {
                    $fine->update([
                        'status' => 'lunas',
                        'payment_status' => 'lunas',
                        'paid_at' => now(),
                        'paid_amount' => $fine->amount,
                    ]);
                } else {
                    $fine->update(['payment_status' => 'ditolak']);
                }
            }
        });

        return back()->with(
            'success',
            $data['action'] === 'setujui'
                ? 'Pembayaran disetujui dan denda terkait telah dilunasi.'
                : 'Pembayaran ditolak. Mahasiswa dapat mengunggah ulang bukti pembayaran.'
        );
    }
}
