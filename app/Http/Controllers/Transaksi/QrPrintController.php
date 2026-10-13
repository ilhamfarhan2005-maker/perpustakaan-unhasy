<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\CetakLabelQrRequest;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrPrintController extends Controller
{
    public function book(Book $buku): View
    {
        return $this->labels(collect([$buku]));
    }

    public function books(CetakLabelQrRequest $request): View
    {
        $books = Book::whereIn('id', $request->validated('books'))
            ->orderBy('title')
            ->get();

        return $this->labels($books);
    }

    public function member(User $anggota): View
    {
        abort_if(blank($anggota->nim_nip), 404, 'Anggota ini belum memiliki NIM/NIP untuk dibuatkan QR.');

        return view('transaksi.qr.member', [
            'anggota' => $anggota,
            'qrImage' => $this->qrData($anggota->nim_nip),
        ]);
    }

    private function labels(Collection $books): View
    {
        return view('transaksi.qr.books', [
            'books' => $books,
            'qrImages' => $books->mapWithKeys(fn (Book $book) => [
                $book->id => $this->qrData($book->kode_inventaris),
            ]),
        ]);
    }

    private function qrData(string $value): string
    {
        return base64_encode(QrCode::format('svg')->size(150)->margin(1)->generate($value));
    }
}
