<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => fake()->sentence(3),
            'isbn' => fake()->unique()->numerify('978##########'),
            'author' => fake()->name(),
            'publisher' => fake()->company(),
            'year' => fake()->numberBetween(2015, 2024),
            'rak_location' => fake()->randomElement(['A-01', 'B-02', 'C-03', 'D-04', 'E-05']),
            'stock' => fake()->numberBetween(1, 10),
            'cover_image' => null,
            'qr_code' => null,
            'format' => 'fisik',
            'digital_url' => null,
            'preview_url' => null,
        ];
    }

    public function fisik(): static
    {
        return $this->state(fn (array $attributes) => [
            'format' => 'fisik',
            'rak_location' => fake()->randomElement(['A-01', 'B-02', 'C-03', 'D-04']),
            'stock' => fake()->numberBetween(1, 10),
            'digital_url' => null,
            'preview_url' => null,
        ]);
    }

    public function digital(): static
    {
        return $this->state(fn (array $attributes) => [
            'format' => 'digital',
            'rak_location' => '-',
            'stock' => 0,
            'digital_url' => 'https://example.org/buku/'.fake()->slug().'.pdf',
            'preview_url' => 'https://example.org/pratinjau/'.fake()->slug(),
        ]);
    }

    public function fisikDigital(): static
    {
        return $this->state(fn (array $attributes) => [
            'format' => 'fisik_digital',
            'rak_location' => fake()->randomElement(['A-01', 'B-02', 'C-03']),
            'stock' => fake()->numberBetween(1, 10),
            'digital_url' => 'https://example.org/buku/'.fake()->slug().'.pdf',
            'preview_url' => 'https://example.org/pratinjau/'.fake()->slug(),
        ]);
    }

    public function stokHabis(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }
}
