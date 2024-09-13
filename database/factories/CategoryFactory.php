<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word(10);

        //Nuevo faker para imagenes
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Mmo\Faker\PicsumProvider($faker));
        $faker->addProvider(new \Mmo\Faker\LoremSpaceProvider($faker));
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'image' => 'categories/' . $faker->picsum('public/storage/categories', 640, 480, false),
            'is_featured' => fake()->boolean(),
            'status' => fake()->boolean()
        ];
    }
}
