<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\MovieList;
use App\Models\User;
use Illuminate\Database\Seeder;

class MovieListSeeder extends Seeder
{
    public function run(): void
    {
        if (Movie::count() === 0) {
            Movie::factory()->count(20)->create();
        }

        if (User::count() === 0) {
            User::factory()->count(5)->create();
        }

        MovieList::factory()
            ->count(10)
            ->create();
    }
}
