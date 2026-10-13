<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Teknologi Informasi',
            'Sastra',
            'Agama',
            'Ekonomi',
            'Pendidikan',
            'Hukum',
            'Sains & Matematika',
            'Sejarah & Filsafat',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

    }
}
