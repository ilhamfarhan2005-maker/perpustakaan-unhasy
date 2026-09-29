<?php

namespace App\Http\Controllers\Opac;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $bukuTerbaru = Book::with('category')->latest()->take(8)->get();
        $totalBuku = Book::count();
        $totalKategori = Category::count();
        $totalTersedia = Book::where('stock', '>', 0)->count();
        $kategoriList = Category::withCount('books')->orderBy('name')->get();

        return view('opac.home', compact(
            'bukuTerbaru',
            'totalBuku',
            'totalKategori',
            'totalTersedia',
            'kategoriList'
        ));
    }
}
