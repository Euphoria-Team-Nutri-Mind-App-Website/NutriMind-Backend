<?php

namespace Tests\Feature\Patient;

use Tests\TestCase;
use App\Models\Doctor;
use App\Models\Patient;

class PatientFeaturesTest extends TestCase
{
    //display all doctors
    public function testPatientShowAllDoctorsSuccess()
    {
        Doctor::factory()->count(10)->create();
        $patient = Patient::factory()->create();
        $response = $this->actingAs($patient, 'patient')->get('/api/doctors');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'doctor_info' => [
                'data' => [
                    '*' => [
                        'name',
                        'image',
                        'qualification',
                        'rate',
                    ],
                ],
            ],
        ]);
    }

    //show doctor profile
    public function testPatientShowDoctorSuccess()
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();
        $response = $this->actingAs($patient, 'patient')->get('/api/doctor/' . $doctor->id);
        $response->assertStatus(200);

        $response->assertJson([
            'status' => true,
            'doctor_info' => [
                'name' => $doctor->name,
                'gender' => $doctor->gender,
                'image' => $doctor->image,
                'qualification' => $doctor->qualification,
                'experience_years' => $doctor->experience_years,
                'rate' => $doctor->rate,
                'price' => $doctor->price,
            ],
        ]);
    }
    //show doctor profile
    public function testPatientSearchDoctorSuccess()
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        $response = $this->actingAs($patient, 'patient')->get('/api/doctors/search', ['name' => 'John']);
        $response->assertStatus(200);
    }

    //show patient recommended calories
    public function testPatientRecommendedCaloriesSuccess()
    {
        $patient = Patient::factory()->create([
            'height' => 170,
            'first_weight' => 70,
            'current_weight' => 65,
            'age' => 30,
            'gender' => 'male',
            'active_status' => 'Slack',
            'calories' => 0,
        ]);

        $response = $this->actingAs($patient, 'patient')->get('api/recommended-calories');

        $response->assertStatus(200);
    }


}

