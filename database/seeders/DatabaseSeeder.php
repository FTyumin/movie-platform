<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

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

        // $this->call(MovieListSeeder::class);
        $this->call(FeedSeeder::class);
    }
}
