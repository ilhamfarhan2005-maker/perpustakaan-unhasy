<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\FinePayment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BuktiPembayaranController extends Controller
{
    public function show(FinePayment $payment): BinaryFileResponse
    {
        $user = request()->user();
        abort_unless(
            in_array($user->role, ['admin', 'pustakawan'], true)
                || ($user->isMahasiswa() && $payment->user_id === $user->id),
            403
        );
        abort_unless($payment->proof_path && Storage::disk('local')->exists($payment->proof_path), 404);

        return response()->file(Storage::disk('local')->path($payment->proof_path));
    }
}
