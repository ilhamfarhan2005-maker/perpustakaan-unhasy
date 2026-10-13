<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'mahasiswa',
            'nim_nip' => fake()->unique()->numerify('202301####'),
            'status' => 'active',
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'nim_nip' => fake()->unique()->numerify('ADM###'),
        ]);
    }

    public function pustakawan(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'pustakawan',
            'nim_nip' => fake()->unique()->numerify('PST###'),
        ]);
    }

    public function mahasiswa(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'mahasiswa',
            'nim_nip' => fake()->unique()->numerify('202301####'),
        ]);
    }

    public function kepalaPerpustakaan(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'kepala_perpustakaan',
            'nim_nip' => fake()->unique()->numerify('KPP###'),
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

