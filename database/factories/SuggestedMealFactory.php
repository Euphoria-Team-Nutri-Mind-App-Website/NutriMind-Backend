<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuggestedMeal>
 */
class SuggestedMealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'details' => fake()->paragraph,
            'calories' => fake()->numberBetween(100, 1000),
            'protein' => fake()->numberBetween(5, 50),
            'fats' => fake()->numberBetween(5, 50),
            'carbs' => fake()->numberBetween(5, 50),
            'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQMG8UVOrvycr3x-k-qwQggPZMHK_aw4hEi2A&usqp=CAU',
        ];
    }
}
