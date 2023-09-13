<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Patient;
use App\Notifications\OTP;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientAuthenticationTest extends TestCase
{
    public function testPatientRegistration(): void
    {
        $response = $this->post('/patient/api/patient/register', [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'=>'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'password_confirmation'=>'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'height' => fake()->randomElement([178,167,150,134,195]),
            'first_weight' => fake()->randomElement([78,67,50,34,95]),
            'age' => fake()->randomElement([20,17,15,34,55]),
            'gender' => fake()->randomElement(['male', 'female']),
            'verfication_code' => fake()->randomElement([1025,5592,2173,4687,4255]),
            'calories' => fake()->randomElement([2006,1592,2173,2687,1255]),
        ]);

        $response->assertStatus(200);
    }


    public function testPatientLoginSuccess(){
        $patient = Patient::factory()->create();

        $response = $this->post('/patient/api/patient/login', [
            'email' => $patient->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }


    public function testPatientGetOtpSuccess(){
        $email = 'xkris@example.org';
        if($patient = Patient::where('email', $email)->first())
        {
            $response = $this->get('/patient/api/patient/generate-otp',[
                $patient->generateOtpCode(), //send otp code
                $patient->notify(new OTP()),
            ]);
            $response->assertStatus(200);
        }
        else
        {
            $response = $this->get('/patient/api/patient/generate-otp');
            $response->assertStatus(200);
            $response->assertSee('There is no account with this email');
        }
    }

    public function testPatientVerifyOtpSuccess(){
        $email = 'xkris@example.org';
        $patient = Patient::where('email', $email)->first();

        if ($patient) {
            $response = $this->get("/patient/api/patient/verify-otp?email=$email&verfication_code=2734");
            $response->assertStatus(200);
            $response->assertSee('Correct verification code');
        } else {
            $this->fail("Your verification code is incorrect");
        }
    }
}
