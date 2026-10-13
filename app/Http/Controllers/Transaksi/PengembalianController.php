<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\CariPengembalianRequest;
use App\Http\Requests\Transaksi\ProsesPengembalianRequest;
use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Services\TransaksiService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class PengembalianController extends Controller
{
    public function __construct(private TransaksiService $service) {}

    public function index(CariPengembalianRequest $request)
    {
        $this->authorize('viewAny', Transaction::class);

        $search = trim((string) $request->validated('search', ''));
        $query = Transaction::with(['user', 'book'])
            ->whereIn('status', ['dipinjam', 'terlambat']);

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                if (preg_match('/^TRX-(\d+)$/i', $search, $matches)) {
                    $builder->orWhere('id', (int) $matches[1]);
                }

                $builder->orWhereHas('user', fn (Builder $user) => $user->where('nim_nip', 'like', "%{$search}%"))
                    ->orWhereHas('book', fn (Builder $book) => $book
                        ->where('isbn', 'like', "%{$search}%")
                        ->orWhere('kode_inventaris', 'like', "%{$search}%"));
            });
        }

        $transaksi = $query->orderBy('due_date')->paginate(15)->withQueryString();
        $pengajuanKembali = Transaction::with(['user', 'book'])
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->whereNotNull('return_requested_at')
            ->oldest('return_requested_at')
            ->get();

        return view('transaksi.pengembalian.index', compact('transaksi', 'pengajuanKembali', 'search'));
    }

    public function store(ProsesPengembalianRequest $request, Transaction $transaksi)
    {
        $this->authorize('update', $transaksi);

        try {
            $hasil = $this->service->prosesPengembalian($transaksi, $request->user());
        } catch (ValidationException $exception) {
            return back()->with('error', $exception->errors()['transaction'][0] ?? 'Pengembalian tidak dapat diproses.');
        }

        $transaksi->loadMissing(['book', 'user', 'studentRequest']);
        ActivityLog::record('transaksi.kembali', "Buku {$transaksi->book->title} dikembalikan oleh {$transaksi->user->name}"
            .($hasil['days_late'] > 0 ? " (terlambat {$hasil['days_late']} hari)" : ''));

        if ($hasil['days_late'] > 0) {
            return back()->with('success', 'Buku dikembalikan. Terlambat '.$hasil['days_late'].' hari. Denda: Rp '
                .number_format($hasil['amount'], 0, ',', '.').' — silakan proses pelunasan di menu Denda.');
        }

        return back()->with('success', "Buku \"{$transaksi->book->title}\" berhasil dikembalikan tepat waktu. Stok ditambah.");
    }
}
