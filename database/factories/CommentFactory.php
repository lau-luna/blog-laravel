<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\article;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->realText(55);
        return [
            'value' => fake()->numberBetween($min=1, $max=5),
            'description' => fake()->realText(255),
            'user_id' => User::all()->random()->id,
            'article_id' => article::all()->random()->id
        ];
    }
}
