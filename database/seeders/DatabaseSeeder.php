<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Comment;
use App\Models\Movie;
use App\Models\Review;
use App\Models\UserRelationship;
use App\Models\WantToWatch;
use App\Models\Seen;
use Maize\Markable\Models\Favorite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Suggestion;
use Faker\Factory as FakerFactory;

class DatabaseSeeder extends Seeder
{
    // php artisan test --testsuite=Feature

    public function run(): void
    {
        $faker = FakerFactory::create();

        // DB::table('users')->insert([
        //     'name' => 'test',
        //     'email' => 'feodorstjumins@gmail.com',
        //     'password' => Hash::make('password'),
    // ]);

         DB::table('users')->insert([
             'name' => 'admin',
             'email' => 'feodor.tjumin28@gmail.com',
             'is_admin' => 1,
             'password' => Hash::make('password'),
         ]);

        // DB::table('users')->insert([
        //     'name' => 'demo',
        //     'email' => 'demo@example.com',
        //     'password' => Hash::make('password'),
        // ]);

        // DB::table('users')->insert([
        //     'name' => 'reviewer',
        //     'email' => 'reviewer@example.com',
        //     'password' => Hash::make('password'),
        // ]);

        $users = User::all();
        $movies = Movie::all();

        // foreach ($users as $user) {
        //     $reviewCount = min($movies->count(), $faker->numberBetween(2, 4));
        //     $pickedMovies = $movies->shuffle()->take($reviewCount);

        //     foreach ($pickedMovies as $movie) {
        //         Review::firstOrCreate(
        //             [
        //                 'user_id' => $user->id,
        //                 'movie_id' => $movie->id,
        //             ],
        //             [
        //                 'title' => $faker->sentence(4),
        //                 'description' => $faker->paragraph(3),
        //                 'rating' => $faker->numberBetween(1, 5),
        //                 'spoilers' => $faker->boolean(15),
        //             ]
        //         );
        //     }
        // }

        // $reviews = Review::all();

        // foreach ($reviews as $review) {
        //     $commentCount = $faker->numberBetween(1, 3);

        //     for ($i = 0; $i < $commentCount; $i++) {
        //         $commenter = $users->where('id', '!=', $review->user_id)->random();
        //         Comment::create([
        //             'user_id' => $commenter->id,
        //             'review_id' => $review->id,
        //             'description' => $faker->sentence(12),
        //         ]);
        //     }
        // }

        // if ($users->count() > 1) {
        //     foreach ($users as $user) {
        //         $followCount = min(3, $users->count() - 1);

        //         $followees = $users
        //             ->where('id', '!=', $user->id)
        //             ->shuffle()
        //             ->take($followCount);

        //         foreach ($followees as $followee) {
        //             UserRelationship::firstOrCreate([
        //                 'follower_id' => $user->id,
        //                 'followee_id' => $followee->id,
        //             ]);
        //         }
        //     }
        // }

        // populate marks tables
        // if ($movies->isNotEmpty()) {

        //     foreach ($users as $user) {
        //     // favorites
        //     $favorites = $movies->shuffle()->take(3);
        //     foreach ($favorites as $movie) {
        //         if (! $user->favorites()->where('markable_id', $movie->id)->exists()) {
        //             Favorite::add($movie, $user);
        //         }
        //     }

        //     // watchlist
        //     $watchlist = $movies->shuffle()->take(4);
        //     foreach ($watchlist as $movie) {
        //         if (! $user->wantToWatch()->where('markable_id', $movie->id)->exists()) {
        //             WantToWatch::add($movie, $user);
        //         }
        //     }

        //     // seen
        //     $seen = $movies->shuffle()->take(5);
        //     foreach ($seen as $movie) {
        //         if (! $user->seenMovies()->where('markable_id', $movie->id)->exists()) {
        //             Seen::add($movie, $user);
        //                 }
        //             }
        //         }
        // }

        if ($users->isNotEmpty()) {
            foreach ($users as $user) {
                $count = $faker->numberBetween(1, 2);

                for ($i = 0; $i < $count; $i++) {
                    Suggestion::create([
                        'user_id' => $user->id,
                        'title' => $faker->sentence(3),
                        'accepted' => $faker->randomElement([true, false, null]),
                    ]);
                }
            }
        }

    }
}
