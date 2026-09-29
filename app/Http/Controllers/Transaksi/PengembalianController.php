<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Services\TransaksiService;
use Illuminate\Validation\ValidationException;

class PengembalianController extends Controller
{
    public function __construct(private TransaksiService $service) {}

    public function index()
    {
        $transaksi = Transaction::with(['user', 'book'])
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->orderBy('due_date')
            ->paginate(15);

        return view('transaksi.pengembalian.index', compact('transaksi'));
    }

    public function store(Transaction $transaksi)
    {
        try {
            $hasil = $this->service->prosesPengembalian($transaksi);
        } catch (ValidationException $exception) {
            return back()->with('error', $exception->errors()['transaction'][0] ?? 'Pengembalian tidak dapat diproses.');
        }

        $transaksi->loadMissing(['book', 'user']);
        ActivityLog::record('transaksi.kembali', "Buku {$transaksi->book->title} dikembalikan oleh {$transaksi->user->name}"
            . ($hasil['days_late'] > 0 ? " (terlambat {$hasil['days_late']} hari)" : ''));

        if ($hasil['days_late'] > 0) {
            return back()->with('success', 'Buku dikembalikan. Terlambat '.$hasil['days_late'].' hari. Denda: Rp '
                .number_format($hasil['amount'], 0, ',', '.').' — silakan proses pelunasan di menu Denda.');
        }

        return back()->with('success', "Buku \"{$transaksi->book->title}\" berhasil dikembalikan tepat waktu. Stok ditambah.");
    }
}