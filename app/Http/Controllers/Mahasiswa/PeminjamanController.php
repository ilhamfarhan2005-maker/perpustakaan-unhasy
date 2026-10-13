<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\RequestBookReturnRequest;
use App\Http\Requests\Mahasiswa\StoreStudentBorrowingRequest;
use App\Models\Book;
use App\Models\Peminjaman;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransaksiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PeminjamanController extends Controller
{
    public function __construct(private TransaksiService $service) {}

    public function store(StoreStudentBorrowingRequest $request, Book $buku): RedirectResponse
    {
        $user = $request->user();

        DB::transaction(function () use ($buku, $user) {
            $user = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $buku = Book::query()->whereKey($buku->id)->lockForUpdate()->firstOrFail();

            if (! $buku->hasPhysicalCopy()) {
                throw ValidationException::withMessages([
                    'buku' => 'Buku digital tidak dapat dipinjam secara fisik.',
                ]);
            }

            [$boleh, $alasan] = $this->service->cekBolehPinjam($user);
            if (! $boleh) {
                throw ValidationException::withMessages(['buku' => $alasan]);
            }

            $alreadyRequested = Peminjaman::where('user_id', $user->id)
                ->where('buku_id', $buku->id)
                ->whereIn('status', ['pending', 'dipinjam'])
                ->exists();
            $alreadyBorrowed = Transaction::where('user_id', $user->id)
                ->where('book_id', $buku->id)
                ->whereIn('status', ['dipinjam', 'terlambat'])
                ->exists();

            if ($alreadyRequested || $alreadyBorrowed) {
                throw ValidationException::withMessages([
                    'buku' => 'Anda sudah memiliki pengajuan aktif untuk buku ini.',
                ]);
            }

            $loanAndRequestCount = Transaction::where('user_id', $user->id)
                ->whereIn('status', ['dipinjam', 'terlambat'])
                ->count();
            $loanAndRequestCount += Peminjaman::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count();
            $maxBorrow = (int) \App\Models\Setting::get('max_borrow_per_user', 3);
            if ($loanAndRequestCount >= $maxBorrow) {
                throw ValidationException::withMessages([
                    'buku' => "Jumlah pinjaman dan pengajuan aktif sudah mencapai batas maksimal ({$maxBorrow}).",
                ]);
            }

            if ($buku->stock < 1) {
                throw ValidationException::withMessages(['buku' => 'Stok buku sedang habis.']);
            }

            Peminjaman::create([
                'user_id' => $user->id,
                'buku_id' => $buku->id,
                'tanggal_pinjam' => today(),
                'status' => 'pending',
                'denda' => 0,
                'workflow_status' => 'Diajukan',
            ]);
        });

        return redirect()->route('katalog.show', $buku)
            ->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }

    public function destroy(\Illuminate\Http\Request $request, Peminjaman $pengajuan): RedirectResponse
    {
        DB::transaction(function () use ($request, $pengajuan) {
            $pengajuan = Peminjaman::query()
                ->whereKey($pengajuan->id)
                ->where('user_id', $request->user()->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pengajuan->status !== 'pending') {
                throw ValidationException::withMessages(['pengajuan' => 'Hanya pengajuan berstatus Diajukan yang dapat dibatalkan.']);
            }

            $pengajuan->update([
                'status' => 'ditolak',
                'workflow_status' => 'Ditolak',
                'rejection_reason' => 'Dibatalkan oleh mahasiswa.',
            ]);
        });

        return back()->with('success', 'Pengajuan peminjaman berhasil dibatalkan.');
    }

    public function requestReturn(RequestBookReturnRequest $request, Transaction $transaksi): RedirectResponse
    {
        DB::transaction(function () use ($request, $transaksi) {
            $transaction = Transaction::query()
                ->whereKey($transaksi->id)
                ->where('user_id', $request->user()->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($transaction->return_requested_at) {
                throw ValidationException::withMessages([
                    'pengajuan' => 'Pengajuan pengembalian buku ini sedang menunggu konfirmasi pustakawan.',
                ]);
            }

            if (! in_array($transaction->status, ['dipinjam', 'terlambat'], true)) {
                throw ValidationException::withMessages([
                    'pengajuan' => 'Transaksi ini sudah selesai atau tidak dapat dikembalikan.',
                ]);
            }

            $requestedAt = now();
            $transaction->update([
                'return_requested_at' => $requestedAt,
                'workflow_status' => 'Menunggu Konfirmasi Kembali',
            ]);
            $transaction->studentRequest?->update([
                'requested_return_at' => $requestedAt,
                'workflow_status' => 'Menunggu Konfirmasi Kembali',
            ]);
        });

        return back()->with('success', 'Pengajuan pengembalian berhasil dikirim dan menunggu konfirmasi pustakawan.');
    }
}
