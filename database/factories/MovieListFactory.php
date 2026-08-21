<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MovieList>
 */
class MovieListFactory extends Factory
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
            'name' => ucfirst(fake()->words(3, true)),
            'description' => fake()->sentence(10),
            'is_private' => fake()->boolean(20),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\MovieList $list) {
            $movies = Movie::inRandomOrder()->take(fake()->numberBetween(3, 8))->pluck('id');

            foreach ($movies as $position => $movieId) {
                $list->addMovie($movieId, $position + 1);
            }
        });
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => true,
        ]);
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => false,
        ]);
    }
}
