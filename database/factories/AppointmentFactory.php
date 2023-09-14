<?php

namespace Database\Factories;

use App\Models\DoctorSetTime;
use App\Models\Patient;
use App\Models\VodafoneCash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name,
            'doctor_id' => 1,
            'doctor_set_time_id' => function () {
                return DoctorSetTime::factory()->create()->id;
            },
            'patient_id' => function () {
                return Patient::factory()->create()->id;
            },
            'payment_method' => 'Vodafone Cash',
            'vodafone_cash_id' => function () {
                return VodafoneCash::factory()->create()->id;
            },
            'status' => 'Active',
        ];
    }
}
