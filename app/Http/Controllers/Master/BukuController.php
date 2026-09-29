<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreBukuRequest;
use App\Http\Requests\Master\UpdateBukuRequest;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $buku = $query->latest()->paginate(15)->withQueryString();
        $kategori = Category::orderBy('name')->get();

        return view('master.buku.index', compact('buku', 'kategori'));
    }

    public function create()
    {
        $kategori = Category::orderBy('name')->get();

        return view('master.buku.create', compact('kategori'));
    }

    public function store(StoreBukuRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $buku = Book::create($data);
        ActivityLog::record('buku.create', "Buku: {$buku->title}");

        return redirect()->route('pustakawan.buku.index')->with('success', "Buku \"{$buku->title}\" berhasil ditambahkan.");
    }

    public function show(Book $buku)
    {
        return view('master.buku.show', compact('buku'));
    }

    public function edit(Book $buku)
    {
        $kategori = Category::orderBy('name')->get();

        return view('master.buku.edit', compact('buku', 'kategori'));
    }

    public function update(UpdateBukuRequest $request, Book $buku)
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($buku->cover_image && Storage::disk('public')->exists($buku->cover_image)) {
                Storage::disk('public')->delete($buku->cover_image);
            }

            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $buku->update($data);
        ActivityLog::record('buku.update', "Buku: {$buku->title}");

        return redirect()->route('pustakawan.buku.index')->with('success', "Buku \"{$buku->title}\" berhasil diperbarui.");
    }

    public function destroy(Book $buku)
    {
        $sedangDipinjam = $buku->transactions()->where('status', 'dipinjam')->exists();
        if ($sedangDipinjam) {
            return back()->with('error', "Buku \"{$buku->title}\" tidak bisa dihapus karena sedang dipinjam.");
        }

        if ($buku->cover_image && Storage::disk('public')->exists($buku->cover_image)) {
            Storage::disk('public')->delete($buku->cover_image);
        }

        $title = $buku->title;
        $buku->delete();
        ActivityLog::record('buku.delete', "Buku: {$title}");

        return redirect()->route('pustakawan.buku.index')->with('success', "Buku \"{$title}\" berhasil dihapus.");
    }
}
