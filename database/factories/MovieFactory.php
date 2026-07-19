<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tmdb_id' => fake()->unique()->numberBetween(1, 1000000),
            'name' => fake()->unique()->sentence(3),
            'year' => fake()->numberBetween(1980, 2025),
            'description' => fake()->paragraph(),
            'language' => 'en',
            'duration' => fake()->numberBetween(80, 180),
            'tmdb_rating' => fake()->randomFloat(1, 1, 10),
        ];
    }
}
