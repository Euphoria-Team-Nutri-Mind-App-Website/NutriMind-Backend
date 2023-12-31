<?php

namespace Tests\Feature\AuthTesting;

use Tests\TestCase;
use App\Models\Doctor;
use App\Notifications\OTP;


class DoctorAuthenticationTest extends TestCase
{
    public function testDoctorRegistration(): void
    {
        $response = $this->post('/doctor/api/doctor/register', [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password'=>'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'password_confirmation'=>'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'gender' => fake()->randomElement(['male', 'female']),
            'qualification' => fake()->sentence(10),
            'national_id' => fake()->phoneNumber(),
            'experience_years' => fake()->randomElement([1,2,3,4,5]),
            'price' => fake()->randomElement([100,250,300,240,255]),
            'credit_card_number' => fake()->creditCardNumber(),
            'vodafone_cash' => '01' . fake()->randomElement(['0', '1', '2', '5']) . fake()->numberBetween(10000000, 99999999),
        ]);

        $response->assertStatus(200);
    }

    public function testDoctorSetWorkTime(): void
    {
        $doctor = Doctor::factory()->create();
        $response = $this->withoutMiddleware()->actingAs($doctor)->post('doctor/api/doctor/work-days', [
        'work_days' => ['saturday', 'sunday','monday','tuesday','wednesday','thursday'],
        'from_to' => 'From 2pm to 9pm',
        ]);

        $response->assertStatus(200);
    }

    public function testDoctorLoginSuccess(){
        $doctor = Doctor::factory()->create();

        $response = $this->post('/doctor/api/doctor/login', [
            'email' => $doctor->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    public function testDoctorGetOtpSuccess(){
        $email = 'dallas.breitenberg@example.org';
        if($doctor = Doctor::where('email', $email)->first())
        {
            $response = $this->get('/doctor/api/doctor/generate-otp',[
                $doctor->generateOtpCode(), //send otp code
                $doctor->notify(new OTP()),
            ]);
            $response->assertStatus(200);
        }
        else
        {
            $response = $this->get('/doctor/api/doctor/generate-otp');
            $response->assertStatus(200);
            $response->assertSee('There is no account with this email');
        }
    }

    public function testDoctorVerifyOtpSuccess(){
        $email = 'hodkiewicz.jaden@example.org';
        $doctor = Doctor::where('email', $email)->first();

        if ($doctor) {
            $response = $this->get("/doctor/api/doctor/verify-otp?email=$email&verfication_code=4255");
            $response->assertStatus(200);
            $response->assertSee('Correct verification code');
        } else {
            $this->fail("Your verification code is incorrect");
        }
    }

    public function testDoctorResetPasswordSuccess(){
        $email = 'hodkiewicz.jaden@example.org';
        $doctor = Doctor::where('email', $email)->first();

        if ($doctor) {
            $response = $this->actingAs($doctor, 'doctor')->post("/doctor/api/doctor/reset-password", [
                'email' => $email,
                'password' => 'hebaasker2124538',
                'password_confirmation' => 'hebaasker2124538',
            ]);

            $response->assertStatus(200);
            $response->assertJson([
                'status' => true,
                'message' => 'Your password has been changed',
            ]);
        }
    }

    public function testDoctorUpdateProfileSuccess(){
        // Create a doctor and login
        $doctor = Doctor::factory()->create();
        $accessToken = $doctor->createToken('Test Token')->accessToken;

        // Update the doctor's profile
        $response = $this->actingAs($doctor, 'doctor')->post('/doctor/api/doctor/update-profile', [
            'name' => 'New Name',
            'email' => 'newemail@example.com',
            'password' => 'password',
        ]);
        $response->assertStatus(200);
    }

    public function testDoctorLogoutSuccess(){
        // Create a doctor and login
        $doctor = Doctor::factory()->create();
        $accessToken = $doctor->createToken('Test Token')->accessToken;

        $response = $this->actingAs($doctor, 'doctor')->get('/doctor/api/doctor/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
