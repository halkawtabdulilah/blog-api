<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Post::factory()->count(100)->create();
        ActivityLog::factory(100)->create();

    }
}
