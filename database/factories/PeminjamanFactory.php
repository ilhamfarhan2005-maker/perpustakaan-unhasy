<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Peminjaman>
 */
class PeminjamanFactory extends Factory
{
    protected $model = Peminjaman::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'buku_id' => Book::factory(),
            'transaction_id' => null,
            'tanggal_pinjam' => today()->toDateString(),
            'tanggal_kembali' => null,
            'requested_return_at' => null,
            'status' => 'pending',
            'denda' => 0,
            'workflow_status' => 'Diajukan',
            'rejection_reason' => null,
        ];
    }

    public function diajukan(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'workflow_status' => 'Diajukan',
            'transaction_id' => null,
            'rejection_reason' => null,
        ]);
    }

    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ditolak',
            'workflow_status' => 'Ditolak',
            'transaction_id' => null,
            'rejection_reason' => 'Buku sedang dalam pemeliharaan atau stok fisik tidak dapat dipinjam.',
        ]);
    }

    public function kedaluwarsa(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ditolak',
            'workflow_status' => 'Kedaluwarsa',
            'transaction_id' => null,
            'rejection_reason' => 'Pengajuan kedaluwarsa karena tidak diverifikasi dalam batas waktu 24 jam.',
            'created_at' => now()->subDays(2),
        ]);
    }

    public function dipinjam(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'dipinjam',
            'workflow_status' => 'Dipinjam',
        ]);
    }
}
