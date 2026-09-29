<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Fine;
use App\Services\TransaksiService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DendaController extends Controller
{
    public function __construct(private TransaksiService $service) {}

    public function index(Request $request)
    {
        $request->validate(['status' => ['nullable', Rule::in(['belum_lunas', 'lunas'])]]);

        $query = Fine::with(['transaction.user', 'transaction.book']);
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $denda = $query->latest()->paginate(15)->withQueryString();
        $totalBelumLunas = Fine::where('status', 'belum_lunas')->sum('amount');
        $totalLunasBulanIni = Fine::where('status', 'lunas')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('paid_amount');

        return view('transaksi.denda.index', compact('denda', 'totalBelumLunas', 'totalLunasBulanIni'));
    }

    public function bayar(Request $request, Fine $fine)
    {
        $data = $request->validate([
            'paid_amount' => ['required', 'numeric', 'min:'.$fine->amount],
        ], ['paid_amount.min' => 'Jumlah bayar minimal Rp '.number_format($fine->amount, 0, ',', '.')]);

        try {
            $fine = $this->service->bayarDenda($fine, (float) $data['paid_amount']);
        } catch (ValidationException $exception) {
            return back()->with('error', $exception->errors()['fine'][0] ?? 'Pelunasan denda tidak dapat diproses.');
        }

        ActivityLog::record('denda.bayar', 'Denda #'.$fine->id.' (Rp '.number_format($fine->amount, 0, ',', '.').') dilunasi.');

        return back()->with('success', 'Pelunasan denda Rp '.number_format($fine->amount, 0, ',', '.').' berhasil dicatat.');
    }
}