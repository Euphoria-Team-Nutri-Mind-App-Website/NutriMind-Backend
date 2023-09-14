<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VodafoneCash>
 */
class VodafoneCashFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price' => fake()->numberBetween(100, 1000),
            'patient_phone_number' => '010' . fake()->numberBetween(10000000, 99999999),
            'doctor_phone_number' => '010' . fake()->numberBetween(10000000, 99999999),
            'receipt_image' => fake()->imageUrl(),
            'email' => fake()->email,
        ];
    }
}
