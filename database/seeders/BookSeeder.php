<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            ['Teknologi Informasi', 'Pemrograman Laravel untuk Pemula', '978602001', 'Andi Pratama', 'Informatika', 2023, 'A-01', 5, 'covers/hRkf4RM5SPoJljy64CadaHe8EMZzHBDYBXaHJW1Y.jpg', 'fisik', null, null],
            ['Teknologi Informasi', 'Algoritma & Struktur Data', '978602002', 'Budi Santoso', 'Elex Media', 2022, '-', 0, 'covers/hRkf4RM5SPoJljy64CadaHe8EMZzHBDYBXaHJW1Y.jpg', 'digital', 'https://example.org/buku/algoritma.pdf', 'https://example.org/pratinjau/algoritma'],
            ['Sastra', 'Laskar Pelangi', '978602003', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'B-01', 4, null, 'fisik_digital', 'https://example.org/buku/laskar-pelangi.pdf', 'https://example.org/pratinjau/laskar-pelangi'],
            ['Agama', 'Fiqih Kontemporer', '978602004', 'Dr. Ahmad', 'Pustaka Ilmu', 2020, 'C-01', 2, 'covers/7ISy0oAJFdddL4omaRYMOaj2UxQ4e1Jyqg1ItTa8.jpg', 'fisik', null, null],
            ['Ekonomi', 'Manajemen Keuangan', '978602005', 'Rina Wijaya', 'Salemba', 2021, 'D-01', 6, 'covers/nV1BpRtJXvSrBodrgmZwhbVOoS2ackTtsFFXdxpw.jpg', 'fisik', null, null],
            ['Pendidikan', 'Psikologi Pendidikan', '978602006', 'Siti Aminah', 'Remaja Rosda', 2019, 'E-01', 3, null, 'fisik', null, null],
        ];

        foreach ($samples as [$categoryName, $title, $isbn, $author, $publisher, $year, $rack, $stock, $coverImage, $format, $digitalUrl, $previewUrl]) {
            $category = Category::where('slug', Str::slug($categoryName))->first();

            Book::updateOrCreate(['isbn' => $isbn], [
                'category_id' => $category?->id,
                'title' => $title,
                'author' => $author,
                'publisher' => $publisher,
                'year' => $year,
                'rak_location' => $rack,
                'stock' => $stock,
                'cover_image' => $coverImage,
                'format' => $format,
                'digital_url' => $digitalUrl,
                'preview_url' => $previewUrl,
            ]);
        }
    }
}
