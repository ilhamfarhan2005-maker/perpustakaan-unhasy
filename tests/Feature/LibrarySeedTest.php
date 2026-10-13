<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibrarySeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_library_seed_data_exists(): void
    {
        $this->artisan('migrate:fresh --seed')->assertSuccessful();

        $this->assertSame(4, User::count());
        $this->assertSame(6, Book::count());
        $this->assertSame('1000', Setting::get('fine_per_day'));
        $this->assertDatabaseHas('books', ['format' => 'fisik']);
        $this->assertDatabaseHas('books', ['format' => 'digital']);
        $this->assertDatabaseHas('books', ['format' => 'fisik_digital']);
        $this->assertDatabaseHas('transactions', [
            'workflow_status' => 'Selesai',
            'return_date' => today()->subDays(5)->toDateString(),
        ]);
        $this->assertDatabaseHas('fine_payments', ['status' => 'menunggu']);
        $this->assertSame(
            'covers/hRkf4RM5SPoJljy64CadaHe8EMZzHBDYBXaHJW1Y.jpg',
            Book::where('isbn', '978602001')->value('cover_image'),
        );
    }

    public function test_home_page_exposes_real_library_statistics(): void
    {
        $this->artisan('migrate:fresh --seed')->assertSuccessful();

        $this->get('/')
            ->assertOk()
            ->assertViewHasAll([
                'totalBooks',
                'availableBooks',
                'totalCategories',
                'categories',
                'latestBooks',
            ]);
    }
}
