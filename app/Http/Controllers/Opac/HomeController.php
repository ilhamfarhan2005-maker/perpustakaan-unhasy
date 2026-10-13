<?php

namespace App\Http\Controllers\Opac;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $availableBooks = Book::whereIn('format', ['fisik', 'fisik_digital'])
            ->where('stock', '>', 0)
            ->count();
        $totalCategories = Category::count();
        $categories = Category::withCount('books')->orderBy('name')->get();
        $latestBooks = Book::with('category')->latest()->take(8)->get();
        $popularBooks = Book::with('category')
            ->withCount('transactions')
            ->orderByDesc('transactions_count')
            ->latest()
            ->take(5)
            ->get();

        return view('welcome', compact(
            'totalBooks',
            'availableBooks',
            'totalCategories',
            'categories',
            'latestBooks',
            'popularBooks',
        ))->with([
            'bukuTerbaru' => $latestBooks,
            'totalBuku' => $totalBooks,
            'totalTersedia' => $availableBooks,
            'totalKategori' => $totalCategories,
            'kategoriList' => $categories,
        ]);
    }
}
