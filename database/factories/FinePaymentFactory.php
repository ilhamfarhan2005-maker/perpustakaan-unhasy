<?php

namespace Database\Factories;

use App\Models\FinePayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinePayment>
 */
class FinePaymentFactory extends Factory
{
    protected $model = FinePayment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'method' => 'qris',
            'amount' => fake()->randomElement([5000, 10000, 15000, 20000]),
            'proof_path' => null,
            'status' => 'menunggu',
            'verified_by' => null,
            'verified_at' => null,
            'rejection_note' => null,
        ];
    }

    public function menunggu(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'menunggu',
            'verified_by' => null,
            'verified_at' => null,
            'rejection_note' => null,
        ]);
    }

    public function terverifikasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terverifikasi',
            'verified_by' => User::factory(),
            'verified_at' => now(),
            'rejection_note' => null,
        ]);
    }

    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ditolak',
            'verified_by' => User::factory(),
            'verified_at' => now(),
            'rejection_note' => 'Bukti pembayaran tidak jelas atau tidak sesuai nominal.',
        ]);
    }
}
