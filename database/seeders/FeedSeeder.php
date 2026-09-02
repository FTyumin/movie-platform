<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\Review;
use App\Models\User;
use App\Models\UserRelationship;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class FeedSeeder extends Seeder
{
    public function run(): void
    {
        if (Movie::count() < 20) {
            Movie::factory()->count(20 - Movie::count())->create();
        }

        if (User::count() < 15) {
            User::factory()->count(15 - User::count())->create();
        }

        $users = User::all();
        $movies = Movie::all();

        $this->seedFollows($users);
        $reviews = $this->seedReviews($users, $movies);
        $this->seedComments($reviews, $users);
        $this->seedLikes($reviews, $users);
        $this->updateMovieRatings($movies);
    }

    private function seedFollows(Collection $users): void
    {
        foreach ($users as $user) {
            $others = $users->where('id', '!=', $user->id);
            $followees = $others->random(min(random_int(2, 5), $others->count()));

            foreach ($followees as $followee) {
                UserRelationship::firstOrCreate([
                    'follower_id' => $user->id,
                    'followee_id' => $followee->id,
                ]);
            }
        }
    }

    private function seedReviews(Collection $users, Collection $movies): Collection
    {
        $reviews = collect();

        foreach ($users as $user) {
            $reviewedMovies = $movies->random(min(random_int(1, 4), $movies->count()));

            foreach ($reviewedMovies as $movie) {
                $review = Review::firstOrCreate(
                    ['user_id' => $user->id, 'movie_id' => $movie->id],
                    [
                        'title' => fake()->sentence(4),
                        'description' => fake()->paragraph(),
                        'rating' => fake()->numberBetween(1, 5),
                        'spoilers' => fake()->boolean(20),
                    ]
                );

                $reviews->push($review);
            }
        }

        return $reviews;
    }

    private function seedComments(Collection $reviews, Collection $users): void
    {
        foreach ($reviews as $review) {
            $commenters = $users->random(min(random_int(0, 5), $users->count()));

            foreach ($commenters as $commenter) {
                Comment::create([
                    'user_id' => $commenter->id,
                    'review_id' => $review->id,
                    'description' => fake()->sentence(random_int(5, 20)),
                ]);
            }
        }
    }

    private function seedLikes(Collection $reviews, Collection $users): void
    {
        foreach ($reviews as $review) {
            $likers = $users->random(min(random_int(0, 8), $users->count()));

            $review->likedBy()->syncWithoutDetaching($likers->pluck('id'));
        }
    }

    private function updateMovieRatings(Collection $movies): void
    {
        foreach ($movies as $movie) {
            if ($movie->reviews()->exists()) {
                $movie->updateRating();
            }
        }
    }
}
