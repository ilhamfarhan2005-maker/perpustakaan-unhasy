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
            // 6 Buku Asli (Dipertahankan)
            ['Teknologi Informasi', 'Pemrograman Laravel untuk Pemula', '978602001', 'Andi Pratama', 'Informatika', 2023, 'A-01', 5, 'covers/hRkf4RM5SPoJljy64CadaHe8EMZzHBDYBXaHJW1Y.jpg', 'fisik', null, null],
            ['Teknologi Informasi', 'Algoritma & Struktur Data', '978602002', 'Budi Santoso', 'Elex Media', 2022, '-', 0, 'covers/hRkf4RM5SPoJljy64CadaHe8EMZzHBDYBXaHJW1Y.jpg', 'digital', 'https://example.org/buku/algoritma.pdf', 'https://example.org/pratinjau/algoritma'],
            ['Sastra', 'Laskar Pelangi', '978602003', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'B-01', 4, null, 'fisik_digital', 'https://example.org/buku/laskar-pelangi.pdf', 'https://example.org/pratinjau/laskar-pelangi'],
            ['Agama', 'Fiqih Kontemporer', '978602004', 'Dr. Ahmad', 'Pustaka Ilmu', 2020, 'C-01', 2, 'covers/7ISy0oAJFdddL4omaRYMOaj2UxQ4e1Jyqg1ItTa8.jpg', 'fisik', null, null],
            ['Ekonomi', 'Manajemen Keuangan', '978602005', 'Rina Wijaya', 'Salemba', 2021, 'D-01', 6, 'covers/nV1BpRtJXvSrBodrgmZwhbVOoS2ackTtsFFXdxpw.jpg', 'fisik', null, null],
            ['Pendidikan', 'Psikologi Pendidikan', '978602006', 'Siti Aminah', 'Remaja Rosda', 2019, 'E-01', 3, null, 'fisik', null, null],

            // Teknologi Informasi (Tambahan 3 buku)
            ['Teknologi Informasi', 'Basis Data Relasional & NoSQL', '978602007', 'Fajar Nugroho', 'Informatika', 2022, 'A-02', 4, null, 'fisik', null, null],
            ['Teknologi Informasi', 'Kecerdasan Buatan dan Pembelajaran Mesin', '978602008', 'Prof. Dr. Irfan Maulana', 'Andi Offset', 2024, '-', 0, null, 'digital', 'https://example.org/buku/ai-ml.pdf', 'https://example.org/pratinjau/ai-ml'],
            ['Teknologi Informasi', 'Keamanan Jaringan dan Kriptografi', '978602009', 'Deni Prasetyo', 'Elex Media', 2023, 'A-03', 3, null, 'fisik_digital', 'https://example.org/buku/cybersecurity.pdf', 'https://example.org/pratinjau/cybersecurity'],

            // Sastra (Tambahan 4 buku)
            ['Sastra', 'Bumi Manusia', '978602010', 'Pramoedya Ananta Toer', 'Hasta Mitra', 2005, 'B-02', 5, null, 'fisik', null, null],
            ['Sastra', 'Ronggeng Dukuh Paruk', '978602011', 'Ahmad Tohari', 'Gramedia Pustaka Utama', 2011, 'B-03', 3, null, 'fisik', null, null],
            ['Sastra', 'Cantik Itu Luka', '978602012', 'Eka Kurniawan', 'Gramedia Pustaka Utama', 2015, '-', 0, null, 'digital', 'https://example.org/buku/cantik-itu-luka.pdf', 'https://example.org/pratinjau/cantik-itu-luka'],
            ['Sastra', 'Hujan Bulan Juni', '978602013', 'Sapardi Djoko Damono', 'Gramedia Pustaka Utama', 2013, 'B-04', 2, null, 'fisik_digital', 'https://example.org/buku/hujan-bulan-juni.pdf', 'https://example.org/pratinjau/hujan-bulan-juni'],

            // Agama (Tambahan 4 buku)
            ['Agama', 'Tafsir Al-Misbah Jilid 1', '978602014', 'M. Quraish Shihab', 'Lentera Hati', 2018, 'C-02', 4, null, 'fisik', null, null],
            ['Agama', 'Sejarah Peradaban Islam', '978602015', 'Badri Yatim', 'Rajawali Pers', 2017, 'C-03', 5, null, 'fisik', null, null],
            ['Agama', 'Studi Islam Komprehensif', '978602016', 'Prof. Dr. Nur Syam', 'Kencana', 2021, '-', 0, null, 'digital', 'https://example.org/buku/studi-islam.pdf', 'https://example.org/pratinjau/studi-islam'],
            ['Agama', 'Metodologi Studi Islam', '978602017', 'Abuddin Nata', 'Rajawali Pers', 2020, 'C-04', 3, null, 'fisik_digital', 'https://example.org/buku/metodologi-islam.pdf', 'https://example.org/pratinjau/metodologi-islam'],

            // Ekonomi (Tambahan 4 buku)
            ['Ekonomi', 'Makroekonomi Teori Pengantar', '978602018', 'Sadono Sukirno', 'Rajawali Pers', 2019, 'D-02', 6, null, 'fisik', null, null],
            ['Ekonomi', 'Akuntansi Keuangan Menengah', '978602019', 'Kieso, Weygandt, Warfield', 'Salemba Empat', 2022, 'D-03', 4, null, 'fisik', null, null],
            ['Ekonomi', 'Ekonomi Syariah: Prinsip dan Praktik', '978602020', 'Adiwarman Karim', 'Rajawali Pers', 2021, '-', 0, null, 'digital', 'https://example.org/buku/ekonomi-syariah.pdf', 'https://example.org/pratinjau/ekonomi-syariah'],
            ['Ekonomi', 'Kewirausahaan dan Inovasi Bisnis', '978602021', 'Kasmir', 'Rajawali Pers', 2023, 'D-04', 5, null, 'fisik_digital', 'https://example.org/buku/kewirausahaan.pdf', 'https://example.org/pratinjau/kewirausahaan'],

            // Pendidikan (Tambahan 4 buku)
            ['Pendidikan', 'Desain Instruksional Modern', '978602022', 'Prof. Dr. Atwi Suparman', 'Erlangga', 2020, 'E-02', 3, null, 'fisik', null, null],
            ['Pendidikan', 'Metodologi Penelitian Pendidikan', '978602023', 'Sugiyono', 'Alfabeta', 2022, 'E-03', 7, null, 'fisik', null, null],
            ['Pendidikan', 'Evaluasi Pembelajaran Berbasis Asesmen Kompetensi', '978602024', 'Zainal Arifin', 'Remaja Rosda', 2021, '-', 0, null, 'digital', 'https://example.org/buku/evaluasi-pembelajaran.pdf', 'https://example.org/pratinjau/evaluasi-pembelajaran'],
            ['Pendidikan', 'Manajemen Pendidikan Tinggi', '978602025', 'Husaini Usman', 'Bumi Aksara', 2019, 'E-04', 2, null, 'fisik_digital', 'https://example.org/buku/manajemen-pt.pdf', 'https://example.org/pratinjau/manajemen-pt'],

            // Hukum (5 buku)
            ['Hukum', 'Pengantar Tata Hukum Indonesia', '978602026', 'C.S.T. Kansil', 'Sinar Grafika', 2018, 'F-01', 4, null, 'fisik', null, null],
            ['Hukum', 'Hukum Perdata Indonesia', '978602027', 'Subekti', 'Intermasa', 2019, 'F-02', 5, null, 'fisik', null, null],
            ['Hukum', 'Asas-Asas Hukum Pidana', '978602028', 'Moeljatno', 'Rineka Cipta', 2020, 'F-03', 3, null, 'fisik', null, null],
            ['Hukum', 'Hukum Tata Negara Kontemporer', '978602029', 'Jimly Asshiddiqie', 'Konstitusi Press', 2022, '-', 0, null, 'digital', 'https://example.org/buku/htn-kontemporer.pdf', 'https://example.org/pratinjau/htn-kontemporer'],
            ['Hukum', 'Hukum Bisnis dan Perlindungan Konsumen', '978602030', 'Munir Fuady', 'Citra Aditya', 2021, 'F-04', 4, null, 'fisik_digital', 'https://example.org/buku/hukum-bisnis.pdf', 'https://example.org/pratinjau/hukum-bisnis'],

            // Sains & Matematika (5 buku)
            ['Sains & Matematika', 'Kalkulus Lanjut dan Aljabar Linier', '978602031', 'Purcell & Varberg', 'Erlangga', 2019, 'G-01', 6, null, 'fisik', null, null],
            ['Sains & Matematika', 'Fisika Dasar untuk Sains dan Teknik', '978602032', 'Halliday & Resnick', 'Erlangga', 2020, 'G-02', 4, null, 'fisik', null, null],
            ['Sains & Matematika', 'Statistika Terapan dan Analisis Data', '978602033', 'Sudjana', 'Tarsito', 2021, 'G-03', 5, null, 'fisik', null, null],
            ['Sains & Matematika', 'Kimia Dasar: Prinsip dan Terapan', '978602034', 'Raymond Chang', 'Erlangga', 2022, '-', 0, null, 'digital', 'https://example.org/buku/kimia-dasar.pdf', 'https://example.org/pratinjau/kimia-dasar'],
            ['Sains & Matematika', 'Pengantar Sains Data dan Visualisasi', '978602035', 'Agus Hendra', 'Andi Offset', 2023, 'G-04', 3, null, 'fisik_digital', 'https://example.org/buku/data-science.pdf', 'https://example.org/pratinjau/data-science'],

            // Sejarah & Filsafat (5 buku)
            ['Sejarah & Filsafat', 'Pengantar Filsafat Ilmu', '978602036', 'Jujun S. Suriasumantri', 'Pustaka Sinar Harapan', 2017, 'H-01', 4, null, 'fisik', null, null],
            ['Sejarah & Filsafat', 'Sejarah Nasional Indonesia Jilid 1', '978602037', 'Sartono Kartodirdjo', 'Balai Pustaka', 2016, 'H-02', 3, null, 'fisik', null, null],
            ['Sejarah & Filsafat', 'Filsafat Barat Klasik hingga Modern', '978602038', 'Bertens', 'Gramedia Pustaka Utama', 2018, 'H-03', 5, null, 'fisik', null, null],
            ['Sejarah & Filsafat', 'Sejarah Peradaban Dunia Kuno', '978602039', 'G. Soebantardjo', 'Kanisius', 2020, '-', 0, null, 'digital', 'https://example.org/buku/sejarah-dunia.pdf', 'https://example.org/pratinjau/sejarah-dunia'],
            ['Sejarah & Filsafat', 'Etika dan Moralitas Sosial', '978602040', 'Frans Magnis-Suseno', 'Gramedia', 2021, 'H-04', 4, null, 'fisik_digital', 'https://example.org/buku/etika-sosial.pdf', 'https://example.org/pratinjau/etika-sosial'],
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
