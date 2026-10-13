<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Fine;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count();
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalBooks = Book::count();
        $totalStock = (int) Book::sum('stock');

        $aktif = Transaction::whereIn('status', ['dipinjam', 'terlambat'])->count();

        $dendaHariIni = (float) Fine::whereDate('created_at', today())->sum('amount');
        $dendaCount = Fine::whereDate('created_at', today())->count();

        $pinjamBulanIni = Transaction::whereMonth('borrow_date', now()->month)
            ->whereYear('borrow_date', now()->year)
            ->count();

        $totalDendaBelumLunas = (float) Fine::where('status', 'belum_lunas')->sum('amount');

        $peminjamanTerbaru = Transaction::with(['user', 'book'])
            ->latest()
            ->take(5)
            ->get();

        $bukuTerfavorit = Transaction::query()
            ->whereMonth('borrow_date', now()->month)
            ->whereYear('borrow_date', now()->year)
            ->select('book_id', DB::raw('COUNT(*) as total'))
            ->groupBy('book_id')
            ->orderByDesc('total')
            ->with('book')
            ->first();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalMahasiswa',
            'totalBooks',
            'totalStock',
            'aktif',
            'dendaHariIni',
            'dendaCount',
            'pinjamBulanIni',
            'totalDendaBelumLunas',
            'peminjamanTerbaru',
            'bukuTerfavorit',
        ));
    }
}
