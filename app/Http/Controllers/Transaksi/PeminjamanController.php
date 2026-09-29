<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransaksiService;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function __construct(private TransaksiService $service) {}

    public function index(Request $request)
    {
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

        return view('transaksi.peminjaman.index', compact('transaksi'));
    }

    public function create()
    {
        return view('transaksi.peminjaman.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nim_nip' => ['required', 'string'],
            'isbn' => ['required', 'string'],
        ], [], ['nim_nip' => 'NIM/NIP', 'isbn' => 'ISBN buku']);

        $user = User::where('nim_nip', $data['nim_nip'])->first();
        if (! $user) {
            return back()->withInput()->with('error', "Anggota dengan NIM/NIP \"{$data['nim_nip']}\" tidak ditemukan.");
        }

        $book = Book::where('isbn', $data['isbn'])->first();
        if (! $book) {
            return back()->withInput()->with('error', "Buku dengan ISBN \"{$data['isbn']}\" tidak ditemukan.");
        }

        [$boleh, $alasan] = $this->service->cekBolehPinjam($user);
        if (! $boleh) {
            return back()->withInput()->with('error', $alasan);
        }

        if ($book->stock < 1) {
            return back()->withInput()->with('error', "Stok buku \"{$book->title}\" sedang habis.");
        }

        $transaksi = $this->service->buatPeminjaman($user, $book, $request->user());
        ActivityLog::record('transaksi.pinjam', "User {$user->name} pinjam buku {$book->title}");

        return redirect()->route('pustakawan.peminjaman.show', $transaksi)
            ->with('success', "Peminjaman berhasil! Buku \"{$book->title}\" untuk {$user->name}. Jatuh tempo: ".$transaksi->due_date->translatedFormat('d F Y'));
    }

    public function show(Transaction $transaksi)
    {
        $transaksi->load(['user', 'book', 'handler', 'fine']);

        return view('transaksi.peminjaman.show', compact('transaksi'));
    }
}