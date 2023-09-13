<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    protected $model = Note::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
            'patient_id' => function () {
                return Patient::factory()->create()->id;
            },
        ];
    }
}
