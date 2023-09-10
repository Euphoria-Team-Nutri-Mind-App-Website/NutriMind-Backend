<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class QuiestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('questionnaires')->insert([
            [
                'question' => 'How are you feeling today?',
                'type' => 'options',
                'options' => json_encode(['Not Bad', 'Good', 'Nice', 'Very good']),
            ],
            [
                'question' => 'Are you on your diet program?',
                'type' => 'options',
                'options' => json_encode(['Yes', 'No', 'Yes, but want to change']),
            ],
            [
                'question' => 'Are your fitness in good state? Are you doing practice continuously',
                'type' => 'options',
                'options' => json_encode(['Yes', 'No', 'Yes, but not regularly']),
            ],

        ]);
    }
}
