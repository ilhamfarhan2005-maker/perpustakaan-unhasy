<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Services\TransaksiService;

class PerpanjanganController extends Controller
{
    public function __construct(private TransaksiService $service) {}

    public function store(Transaction $transaksi)
    {
        [$sukses, $hasil] = $this->service->perpanjang($transaksi);
        if (! $sukses) {
            return back()->with('error', $hasil);
        }

        ActivityLog::record('transaksi.perpanjang', "Transaksi #{$transaksi->id} diperpanjang hingga {$hasil->due_date->format('d/m/Y')}");

        return back()->with('success', 'Perpanjangan berhasil. Jatuh tempo baru: '
            .$hasil->due_date->translatedFormat('d F Y')." (perpanjangan ke-{$hasil->extension_count}).");
    }
}