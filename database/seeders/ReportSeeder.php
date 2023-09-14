<?php

namespace Database\Seeders;

use App\Models\VodafoneCash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VodafoneCash::factory()->count(12)->create();
    }
}
