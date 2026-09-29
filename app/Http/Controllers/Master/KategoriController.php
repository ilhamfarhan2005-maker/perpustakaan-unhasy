<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreKategoriRequest;
use App\Http\Requests\Master\UpdateKategoriRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('books');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $kategori = $query->latest()->paginate(15)->withQueryString();

        return view('master.kategori.index', compact('kategori'));
    }

    public function create()
    {
        return view('master.kategori.create');
    }

    public function store(StoreKategoriRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $kategori = Category::create($data);
        ActivityLog::record('kategori.create', "Kategori: {$kategori->name}");

        return redirect()->route('pustakawan.kategori.index')->with('success', "Kategori \"{$kategori->name}\" berhasil ditambahkan.");
    }

    public function edit(Category $kategori)
    {
        return view('master.kategori.edit', compact('kategori'));
    }

    public function update(UpdateKategoriRequest $request, Category $kategori)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $kategori->update($data);
        ActivityLog::record('kategori.update', "Kategori: {$kategori->name}");

        return redirect()->route('pustakawan.kategori.index')->with('success', "Kategori \"{$kategori->name}\" berhasil diperbarui.");
    }

    public function destroy(Category $kategori)
    {
        if ($kategori->books()->exists()) {
            return back()->with('error', "Kategori \"{$kategori->name}\" tidak bisa dihapus karena masih dipakai oleh {$kategori->books()->count()} buku.");
        }

        $name = $kategori->name;
        $kategori->delete();
        ActivityLog::record('kategori.delete', "Kategori: {$name}");

        return redirect()->route('pustakawan.kategori.index')->with('success', "Kategori \"{$name}\" berhasil dihapus.");
    }
}
