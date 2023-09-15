<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'doctor_id' => function () {
                return Doctor::factory()->create()->id;
            },
            'patient_id' => function () {
                // Replace User::class with the actual class name of your User model
                return Patient::factory()->create()->id;
            },
            'rate' => $this->faker->numberBetween(1, 5),
        ];
    }
}
