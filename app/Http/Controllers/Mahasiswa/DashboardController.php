<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Models\Peminjaman;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $linkedTransactions = Peminjaman::where('user_id', $user->id)
            ->whereNotNull('transaction_id')
            ->select('transaction_id');
        $pinjamanAktif = Peminjaman::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'dipinjam'])
            ->count()
            + Transaction::where('user_id', $user->id)
                ->whereIn('status', ['dipinjam', 'terlambat'])
                ->whereNotIn('id', $linkedTransactions)
                ->count();

        $dendaBelumLunas = Fine::whereHas('transaction', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'belum_lunas')->sum('amount')
            + Peminjaman::where('user_id', $user->id)->sum('denda');

        $riwayatCount = Transaction::where('user_id', $user->id)->count()
            - Transaction::where('user_id', $user->id)
                ->whereIn('id', $linkedTransactions)
                ->count()
            + Peminjaman::where('user_id', $user->id)->count();

        $peminjaman = Peminjaman::with('buku')
            ->where('user_id', $user->id)
            ->latest('tanggal_pinjam')
            ->take(5)
            ->get();

        return view('mahasiswa.dashboard', compact(
            'pinjamanAktif',
            'dendaBelumLunas',
            'riwayatCount',
            'peminjaman',
        ));
    }
}
