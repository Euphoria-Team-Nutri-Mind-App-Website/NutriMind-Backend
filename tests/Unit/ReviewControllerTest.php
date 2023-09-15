<?php

namespace Tests\Unit;

use App\Http\Controllers\API\ReviewController;
use App\Http\Requests\ReviewRequest;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    // use DatabaseTransactions;
    // use WithFaker;

    private $reviewController;
    private $patient;
    protected function setUp(): void
    {
        parent::setUp();
        $this->patient = Patient::factory()->create();
        $this->actingAs($this->patient);
        $this->reviewController = new ReviewController();
    }

    public function testIndexReturnsDoctorWithReviewById()
    {
        $doctor = Doctor::factory()->create();
        $review = Review::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => Auth()->user()->id,
        ]);

        $request = new Request();
        $request->merge(['id' => $doctor->id]);

        $response = $this->reviewController->index($request);
        $result = $response->getData();

        $this->assertTrue($result->success);
        $this->assertNull($result->message);
        $this->assertEquals($doctor->rate, $result->data->rate);
    }

    public function testIndexReturnsUnauthorizedWithoutId()
    {
        $request = new Request();

        $response = $this->reviewController->index($request);
        $result = $response->getData();

        $this->assertFalse($result->success);
        $this->assertEquals('You are not authorized to access this information.', $result->message);
    }

    public function testStoreUpdatesExistingReview()
    {
        $doctor = Doctor::factory()->create();
        $existingReview = Review::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => Auth()->user()->id
        ]);

        $data = [
            'doctor_id' => $doctor->id,
            'patient_id' =>  Auth()->user()->id,
            'rate' => 4,
        ];

        $request = ReviewRequest::create('/api/reviews', 'POST', $data);

        $validator = Validator::make($data, [
            'doctor_id' => 'required',
            'patient_id' => 'required',
            'rate' => 'required',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException('Invalid request data');
        }

        $response = $this->reviewController->store($request);
        // $result = $response->getData();

        // $response->assertStatus(200);
        // $this->assertTrue($result->success);
        // $this->assertEquals('Review added successfully.', $result->message);

        // $existingReview->refresh();
        // $this->assertEquals(4, $existingReview->rate);
    }

    public function testStoreCreatesNewReview()
    {
        $doctor = Doctor::factory()->create();

        $request = ReviewRequest::create('/api/reviews', 'POST', [
            'doctor_id' => $doctor->id,
            'patient_id' => $this->faker->randomNumber(),
            'rate' => 5,
        ]);

        $response = $this->reviewController->store($request);
        $result = $response->getData();

        $this->assertTrue($result->success);
        $this->assertEquals('Review added successfully.', $result->message);

        $this->assertDatabaseHas('reviews', [
            'doctor_id' => $doctor->id,
            'rate' => 5,
        ]);
    }
}
