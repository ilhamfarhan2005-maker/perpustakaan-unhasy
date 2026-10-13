<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\IdentifikasiPeminjamanRequest;
use App\Http\Requests\Transaksi\ProsesPengajuanRequest;
use App\Http\Requests\Transaksi\StorePeminjamanRequest;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Fine;
use App\Models\Peminjaman;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransaksiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function __construct(private TransaksiService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Transaction::class);

        $query = Transaction::with(['user', 'book', 'handler'])
            ->whereIn('status', ['dipinjam', 'terlambat']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->whereHas('user', fn ($userQuery) => $userQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('nim_nip', 'like', "%{$search}%"))
                    ->orWhereHas('book', fn ($bookQuery) => $bookQuery->where('title', 'like', "%{$search}%"));
            });
        }

        $transaksi = $query->latest('borrow_date')->paginate(15)->withQueryString();
        $pengajuanPinjam = Peminjaman::with(['user', 'buku'])
            ->where('status', 'pending')
            ->latest()
            ->get();
        $pengajuanKembali = Transaction::with(['user', 'book'])
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->whereNotNull('return_requested_at')
            ->oldest('return_requested_at')
            ->get();

        return view('transaksi.peminjaman.index', compact('transaksi', 'pengajuanPinjam', 'pengajuanKembali'));
    }

    public function create(): View
    {
        $this->authorize('create', Transaction::class);

        return view('transaksi.peminjaman.create');
    }


    public function identify(IdentifikasiPeminjamanRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = [];

        if (filled($data['nim_nip'] ?? null)) {
            $user = User::where('nim_nip', $data['nim_nip'])->first();
            $result['member'] = $user ? [
                'name' => $user->name,
                'nim_nip' => $user->nim_nip,
                'active_loans' => $user->transactions()->whereIn('status', ['dipinjam', 'terlambat'])->count(),
                'unpaid_fine' => Fine::where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhereHas('transaction', fn ($transaction) => $transaction->where('user_id', $user->id));
                })->where(function ($query) {
                    $query->where('status', 'belum_lunas')
                        ->orWhere(function ($status) {
                            $status->where('status', '!=', 'lunas')
                                ->where('payment_status', '!=', 'lunas');
                        });
                })->exists(),
            ] : null;
        }

        if (filled($data['isbn'] ?? null)) {
            $book = Book::where('isbn', $data['isbn'])
                ->orWhere('kode_inventaris', $data['isbn'])
                ->first();
            $result['book'] = $book ? [
                'title' => $book->title,
                'isbn' => $book->isbn,
                'kode_inventaris' => $book->kode_inventaris,
                'stock' => $book->hasPhysicalCopy() ? $book->stock : null,
                'format' => $book->format,
                'borrowable' => $book->isAvailable(),
            ] : null;
        }

        return response()->json($result);
    }

    public function store(StorePeminjamanRequest $request): RedirectResponse
    {
        $this->authorize('create', Transaction::class);

        $data = $request->validated();
        $user = User::where('nim_nip', $data['nim_nip'])->first();
        if (! $user) {
            return back()->withInput()->with('error', "Anggota dengan NIM/NIP \"{$data['nim_nip']}\" tidak ditemukan.");
        }

        $book = Book::where('isbn', $data['isbn'])
            ->orWhere('kode_inventaris', $data['isbn'])
            ->first();
        if (! $book) {
            return back()->withInput()->with('error', "Buku dengan ISBN/kode inventaris \"{$data['isbn']}\" tidak ditemukan.");
        }

        if (! $book->hasPhysicalCopy()) {
            return back()->withInput()->with('error', 'Buku digital tidak dapat dipinjam secara fisik.');
        }

        if ($book->stock < 1) {
            return back()->withInput()->with('error', "Stok buku \"{$book->title}\" sedang habis.");
        }

        try {
            $transaksi = $this->service->buatPeminjaman($user, $book, $request->user());
        } catch (ValidationException $exception) {
            return back()->withInput()->with('error', collect($exception->errors())->flatten()->first()
                ?? 'Peminjaman tidak dapat diproses.');
        }

        ActivityLog::record('transaksi.pinjam', "User {$user->name} pinjam buku {$book->title}");

        return redirect()->route('pustakawan.peminjaman.show', $transaksi)
            ->with('success', "Peminjaman berhasil! Buku \"{$book->title}\" untuk {$user->name}. Jatuh tempo: ".$transaksi->due_date->translatedFormat('d F Y'));
    }

    public function show(Transaction $transaksi)
    {
        $this->authorize('view', $transaksi);

        $transaksi->load(['user', 'book', 'handler', 'fine']);

        return view('transaksi.peminjaman.show', compact('transaksi'));
    }

    public function processRequest(ProsesPengajuanRequest $request, Peminjaman $pengajuan): RedirectResponse
    {
        $action = $request->validated('action');

        try {
            DB::transaction(function () use ($action, $pengajuan, $request) {
                $pengajuan = Peminjaman::whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();

                if ($action === 'setujui') {
                    if ($pengajuan->status !== 'pending') {
                        throw ValidationException::withMessages(['pengajuan' => 'Pengajuan ini sudah diproses.']);
                    }

                    $book = Book::findOrFail($pengajuan->buku_id);
                    $transaction = $this->service->buatPeminjaman($pengajuan->user, $book, $request->user());
                    $pengajuan->update([
                        'status' => 'dipinjam',
                        'transaction_id' => $transaction->id,
                        'tanggal_pinjam' => $transaction->borrow_date,
                        'workflow_status' => 'Dipinjam',
                    ]);
                    $transaction->update([
                        'approved_by' => $request->user()->id,
                        'requested_at' => $pengajuan->created_at,
                    ]);

                    return;
                }

                if ($action === 'tolak') {
                    if ($pengajuan->status !== 'pending') {
                        throw ValidationException::withMessages(['pengajuan' => 'Pengajuan ini sudah diproses.']);
                    }

                    $pengajuan->update([
                        'status' => 'ditolak',
                        'workflow_status' => 'Ditolak',
                        'rejection_reason' => $request->validated('alasan_penolakan'),
                    ]);

                    return;
                }

                if ($pengajuan->status !== 'dipinjam' || ! $pengajuan->transaction_id) {
                    throw ValidationException::withMessages(['pengajuan' => 'Pengajuan pengembalian tidak ditemukan atau sudah diproses.']);
                }

                $transaction = Transaction::whereKey($pengajuan->transaction_id)->lockForUpdate()->firstOrFail();
                if (! $transaction->return_requested_at && ! $pengajuan->requested_return_at) {
                    throw ValidationException::withMessages(['pengajuan' => 'Pengajuan pengembalian tidak ditemukan atau sudah diproses.']);
                }

                if (! $transaction->return_requested_at) {
                    $transaction->update([
                        'return_requested_at' => $pengajuan->requested_return_at,
                        'workflow_status' => 'Menunggu Konfirmasi Kembali',
                    ]);
                }

                $this->service->prosesPengembalian($transaction, $request->user());
            });
        } catch (ValidationException $exception) {
            return back()->with('error', collect($exception->errors())->flatten()->first()
                ?? 'Pengajuan tidak dapat diproses.');
        }

        $message = match ($action) {
            'setujui' => 'Pengajuan peminjaman disetujui dan transaksi berhasil dicatat.',
            'tolak' => 'Pengajuan peminjaman ditolak.',
            default => 'Pengembalian dikonfirmasi. Stok dan denda telah diperbarui.',
        };

        return back()->with('success', $message);
    }
}
