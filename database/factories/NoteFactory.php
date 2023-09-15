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
            'body' => fake()->paragraph,
            'patient_id' => function () {
                return Auth()->user()->id;
            }
        ];
    }
}
