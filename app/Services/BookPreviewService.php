<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BookPreviewService
{
    public function store(Book $book, UploadedFile $file, int $pages): ?string
    {
        $path = $file->store('digital-books', 'local');
        $book->update(['file_path' => $path, 'file_size' => $file->getSize(), 'preview_pages' => $pages, 'preview_path' => null]);

        if (! class_exists(\setasign\Fpdi\Fpdi::class)) {
            return 'FPDI belum tersedia; file asli disimpan tanpa pratinjau.';
        }

        try {
            $source = Storage::disk('local')->path($path);
            $preview = 'digital-previews/'.bin2hex(random_bytes(20)).'.pdf';
            $target = Storage::disk('local')->path($preview);
            $pdf = new \setasign\Fpdi\Fpdi();
            $count = min($pdf->setSourceFile($source), max(1, $pages));
            for ($page = 1; $page <= $count; $page++) {
                $template = $pdf->importPage($page);
                $size = $pdf->getTemplateSize($template);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($template);
            }
            $pdf->Output('F', $target);
            $book->update(['preview_path' => $preview]);
            return null;
        } catch (Throwable) {
            return 'PDF tidak dapat diproses menjadi pratinjau. File asli tetap tersimpan dengan aman.';
        }
    }
}
