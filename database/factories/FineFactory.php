<?php

namespace Database\Factories;

use App\Models\Fine;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fine>
 */
class FineFactory extends Factory
{
    protected $model = Fine::class;

    public function definition(): array
    {
        $daysLate = fake()->numberBetween(1, 10);
        $amount = $daysLate * 1000;

        return [
            'transaction_id' => Transaction::factory(),
            'user_id' => fn (array $attributes) => Transaction::find($attributes['transaction_id'])?->user_id ?? User::factory(),
            'amount' => $amount,
            'days_late' => $daysLate,
            'paid_at' => null,
            'paid_amount' => null,
            'status' => 'belum_lunas',
            'payment_status' => 'belum_dibayar',
        ];
    }

    public function belumLunas(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'belum_lunas',
            'payment_status' => 'belum_dibayar',
            'paid_at' => null,
            'paid_amount' => null,
        ]);
    }

    public function lunas(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'lunas',
            'payment_status' => 'lunas',
            'paid_at' => now(),
            'paid_amount' => $attributes['amount'] ?? 5000,
        ]);
    }

    public function menungguVerifikasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'belum_lunas',
            'payment_status' => 'menunggu_verifikasi',
        ]);
    }
}
