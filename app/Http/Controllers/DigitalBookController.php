<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Support\Facades\Storage;

class DigitalBookController extends Controller
{
    public function preview(Book $buku)
    {
        abort_unless($buku->preview_path && Storage::disk('local')->exists($buku->preview_path), 404);
        return response()->file(Storage::disk('local')->path($buku->preview_path), ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline']);
    }

    public function reader(Book $buku)
    {
        abort_unless($buku->preview_path, 404);
        return view('opac.preview', compact('buku'));
    }

    public function original(Book $buku)
    {
        abort_unless(in_array(request()->user()->role, ['admin', 'pustakawan'], true), 403);
        abort_unless($buku->file_path && Storage::disk('local')->exists($buku->file_path), 404);
        return Storage::disk('local')->download($buku->file_path, $buku->isbn.'.pdf');
    }
}
