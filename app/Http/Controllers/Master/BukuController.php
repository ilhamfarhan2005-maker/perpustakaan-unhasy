<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreBukuRequest;
use App\Http\Requests\Master\UpdateBukuRequest;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Category;
use App\Services\BookPreviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BukuController extends Controller
{
    public function __construct(private BookPreviewService $previews) {}
    public function index(Request $request)
    {
        $this->authorize('viewAny', Book::class);

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

        if (in_array($request->input('format'), Book::FORMATS, true)) {
            $query->where('format', $request->input('format'));
        }

        $buku = $query->latest()->paginate(15)->withQueryString();
        $kategori = Category::orderBy('name')->get();

        return view('master.buku.index', compact('buku', 'kategori'));
    }

    public function create()
    {
        $this->authorize('create', Book::class);

        $kategori = Category::orderBy('name')->get();

        return view('master.buku.create', compact('kategori'));
    }

    public function store(StoreBukuRequest $request)
    {
        $this->authorize('create', Book::class);

        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $data = $this->normalizeFormatFields($data);

        try {
            $buku = Book::create($data);

            $fileName = 'qrcodes/book_' . $buku->id . '.svg';
            $qrImage = QrCode::format('svg')->size(300)->generate($buku->kode_inventaris);

            Storage::disk('public')->put($fileName, $qrImage);

            $buku->qr_code = $fileName;
            $buku->save();

            $warning = $request->hasFile('digital_file') ? $this->previews->store($buku, $request->file('digital_file'), (int) \App\Models\Setting::get('digital_preview_pages', 10)) : null;
            ActivityLog::record('buku.create', "Buku: {$buku->title}");

            $response = redirect()->route('pustakawan.buku.index')->with('success', "Buku \"{$buku->title}\" berhasil ditambahkan.");
            if ($warning) $response->with('error', $warning);
            return $response;
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal membuat QR Code untuk buku baru.')->withInput();
        }
    }

    public function show(Book $buku)
    {
        $this->authorize('view', $buku);

        return view('master.buku.show', compact('buku'));
    }

    public function edit(Book $buku)
    {
        $this->authorize('update', $buku);

        $kategori = Category::orderBy('name')->get();

        return view('master.buku.edit', compact('buku', 'kategori'));
    }

    public function update(UpdateBukuRequest $request, Book $buku)
    {
        $this->authorize('update', $buku);

        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($buku->cover_image && Storage::disk('public')->exists($buku->cover_image)) {
                Storage::disk('public')->delete($buku->cover_image);
            }

            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $data = $this->normalizeFormatFields($data);
        $buku->update($data);
        $warning = $request->hasFile('digital_file') ? $this->previews->store($buku, $request->file('digital_file'), (int) \App\Models\Setting::get('digital_preview_pages', 10)) : null;
        ActivityLog::record('buku.update', "Buku: {$buku->title}");

        $response = redirect()->route('pustakawan.buku.index')->with('success', "Buku \"{$buku->title}\" berhasil diperbarui.");
        if ($warning) $response->with('error', $warning);
        return $response;
    }

    public function destroy(Book $buku)
    {
        $this->authorize('delete', $buku);

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

    private function normalizeFormatFields(array $data): array
    {
        if ($data['format'] === 'digital') {
            $data['stock'] = 0;
            $data['rak_location'] = '-';
        } elseif ($data['format'] === 'fisik') {
            $data['digital_url'] = null;
            $data['preview_url'] = null;
        }

        if (array_key_exists('kode_inventaris', $data)) {
            $data['kode_inventaris'] = filled($data['kode_inventaris'])
                ? trim($data['kode_inventaris'])
                : null;
        }

        return $data;
    }
}
