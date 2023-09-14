<?php

namespace Database\Factories;

use App\Models\Appointment;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker = Faker::create();

        return [
            'diagnosis_of_his_state' => $faker->word,
            'description' => $faker->paragraph,
            'appointment_id' => function () {
                return Appointment::factory()->create()->id;
            },
        ];
    }
}
