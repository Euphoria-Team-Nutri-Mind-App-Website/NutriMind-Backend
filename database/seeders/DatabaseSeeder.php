<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\AppointmentsSeeder;
use Database\Seeders\DoctorSetTimesSeeder;
use Database\Seeders\DoctorWorkDaysSeeder;
use Database\Seeders\VodafoneCashesSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\ChatSeeder;
use Database\Seeders\QouteSeeder;
use Database\Seeders\DoctorSeeder;
use Database\Seeders\MessageSeeder;
use Database\Seeders\PatientSeeder;
use Database\Seeders\QuiestionnaireSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DoctorSeeder::class,
            PatientSeeder::class,
            ChatSeeder::class,
            MessageSeeder::class,
            QouteSeeder::class,
            QuiestionnaireSeeder::class,
            DoctorWorkDaySeeder::class,
            VodafoneCashSeeder::class,
            DoctorSetTimeSeeder::class,
            AppointmentSeeder::class,
            ReportSeeder::class,
            SuggestedMealSeeder::class
        ]);
    }
}
