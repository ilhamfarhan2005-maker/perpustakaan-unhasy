<?php

namespace App\Http\Controllers\Opac;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category');

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->whereHas('category', function ($builder) use ($request) {
                $builder->where('slug', $request->kategori);
            });
        }

        if ($request->filled('tersedia') && $request->tersedia === '1') {
            $query->whereIn('format', ['fisik', 'fisik_digital'])
                ->where('stock', '>', 0);
        }

        $buku = $query->latest()->paginate(12)->withQueryString();
        $kategoriList = Category::withCount('books')->orderBy('name')->get();

        return view('opac.katalog', compact('buku', 'kategoriList'));
    }

    public function show(Book $buku)
    {
        $serupa = Book::where('category_id', $buku->category_id)
            ->where('id', '!=', $buku->id)
            ->take(4)
            ->get();
        $pengajuanAktif = auth()->check() && auth()->user()->isMahasiswa()
            ? Peminjaman::where('user_id', auth()->id())
                ->where('buku_id', $buku->id)
                ->whereIn('status', ['pending', 'dipinjam'])
                ->first()
            : null;

        return view('opac.detail', compact('buku', 'serupa', 'pengajuanAktif'));
    }
}
