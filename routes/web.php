<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Master\BukuController;
use App\Http\Controllers\Master\KategoriController;
use App\Http\Controllers\Opac\HomeController;
use App\Http\Controllers\Opac\KatalogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Transaksi\DendaController;
use App\Http\Controllers\Transaksi\PeminjamanController;
use App\Http\Controllers\Transaksi\PengembalianController;
use App\Http\Controllers\Transaksi\PerpanjanganController;
use Illuminate\Support\Facades\Route;

// ───── PUBLIK (TANPA LOGIN) ─────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{buku}', [KatalogController::class, 'show'])->name('katalog.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'pustakawan' => redirect()->route('pustakawan.dashboard'),
            'kepala_perpustakaan' => redirect()->route('eksekutif.dashboard'),
            default => redirect()->route('mahasiswa.dashboard'),
        };
    })->name('dashboard');

    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Mahasiswa\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/riwayat', [\App\Http\Controllers\Mahasiswa\RiwayatController::class, 'index'])->name('riwayat');
    });

    Route::middleware('role:pustakawan,admin')->prefix('pustakawan')->name('pustakawan.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Pustakawan\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::get('/peminjaman/{transaksi}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
        Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
        Route::post('/pengembalian/{transaksi}', [PengembalianController::class, 'store'])->name('pengembalian.store');
        Route::post('/perpanjangan/{transaksi}', [PerpanjanganController::class, 'store'])->name('perpanjangan.store');
        Route::get('/denda', [DendaController::class, 'index'])->name('denda.index');
        Route::post('/denda/{fine}/bayar', [DendaController::class, 'bayar'])->name('denda.bayar');
        Route::resource('kategori', KategoriController::class)->except(['show'])->parameters(['kategori' => 'kategori']);
        Route::resource('buku', BukuController::class)->parameters(['buku' => 'buku']);
    });

    Route::middleware('role:kepala_perpustakaan')->prefix('eksekutif')->name('eksekutif.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Eksekutif\DashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
        Route::resource('pengguna', UserController::class)->except(['show']);
        Route::get('/pengaturan', [\App\Http\Controllers\Admin\SettingController::class, 'edit'])->name('pengaturan.edit');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
