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
    }
}
