<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VodafoneCash;
use Faker\Factory as Faker;

class VodafoneCashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VodafoneCash::factory()->count(12)->create();

    }
}
