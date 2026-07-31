<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Review;
use App\Models\User;
use App\Models\Movie;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'movie_id' => Movie::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'rating' => $this->faker->numberBetween(1, 5),
            'spoilers' => $this->faker->boolean(20),
        ];
    }
    protected $model = Review::class;
}
