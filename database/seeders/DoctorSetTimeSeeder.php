<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DoctorSetTime;
use Faker\Factory as Faker;

class DoctorSetTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DoctorSetTime::factory()->count(2)->create();

    }
}
