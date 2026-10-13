<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Transaction;

class RiwayatController extends Controller
{
    public function index()
    {
        $riwayat = Transaction::with('book')
            ->where('user_id', auth()->id())
            ->latest('borrow_date')
            ->paginate(10);
        $pengajuan = Peminjaman::with('buku')
            ->where('user_id', auth()->id())
            ->whereNull('transaction_id')
            ->latest('tanggal_pinjam')
            ->paginate(10, ['*'], 'pengajuan_page');

        return view('mahasiswa.riwayat', compact('riwayat', 'pengajuan'));
    }
}
