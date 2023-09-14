<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DoctorWorkDay;
use Faker\Factory as Faker;

class DoctorWorkDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DoctorWorkDay::factory()->count(12)->create();

    }
}
