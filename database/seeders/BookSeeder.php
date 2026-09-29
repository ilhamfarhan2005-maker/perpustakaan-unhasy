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
            ['Teknologi Informasi', 'Pemrograman Laravel untuk Pemula', '978602001', 'Andi Pratama', 'Informatika', 2023, 'A-01', 5],
            ['Teknologi Informasi', 'Algoritma & Struktur Data', '978602002', 'Budi Santoso', 'Elex Media', 2022, 'A-02', 3],
            ['Sastra', 'Laskar Pelangi', '978602003', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'B-01', 4],
            ['Agama', 'Fiqih Kontemporer', '978602004', 'Dr. Ahmad', 'Pustaka Ilmu', 2020, 'C-01', 2],
            ['Ekonomi', 'Manajemen Keuangan', '978602005', 'Rina Wijaya', 'Salemba', 2021, 'D-01', 6],
            ['Pendidikan', 'Psikologi Pendidikan', '978602006', 'Siti Aminah', 'Remaja Rosda', 2019, 'E-01', 3],
        ];

        foreach ($samples as [$categoryName, $title, $isbn, $author, $publisher, $year, $rack, $stock]) {
            $category = Category::where('slug', Str::slug($categoryName))->first();

            Book::updateOrCreate(['isbn' => $isbn], [
                'category_id' => $category?->id,
                'title' => $title,
                'author' => $author,
                'publisher' => $publisher,
                'year' => $year,
                'rak_location' => $rack,
                'stock' => $stock,
            ]);
        }
    }
}
