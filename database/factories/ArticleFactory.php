<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->realText(55);
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Mmo\Faker\PicsumProvider($faker));
        $faker->addProvider(new \Mmo\Faker\LoremSpaceProvider($faker));
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'introduction' => fake()->realText(255),
            'image' => 'articles/' . $faker->picsum('public/storage/articles', 640, 480, false),
            'body' => fake()->text(2000),
            'status' => fake()->boolean(),
            'user_id' => User::all()->random()->id,
            'category_id' => Category::all()->random()->id
        ];

    }
}
