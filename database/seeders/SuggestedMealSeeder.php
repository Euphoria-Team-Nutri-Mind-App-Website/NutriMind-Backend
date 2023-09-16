<?php

namespace Database\Seeders;

use App\Models\SuggestedMeal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuggestedMealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SuggestedMeal::factory()->count(12)->create();
    }
}
