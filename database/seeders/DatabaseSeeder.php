<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Review;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        // DB::table('users')->insert([
        //     'name' => 'admin',
        //     'email' => 'feodor.tjumin28@gmail.com',
        //     'is_admin' => 1,
        //     'password' => Hash::make('password'),
        // ]);

        // DB::table('users')->insert([
        //     'name' => 'demo',
        //     'email' => 'demo@example.com',
        //     'password' => Hash::make('password'),
        // ]);

        Review::factory()
            ->count(5)
            ->create();

        $this->call(MovieListSeeder::class);
    }
}
