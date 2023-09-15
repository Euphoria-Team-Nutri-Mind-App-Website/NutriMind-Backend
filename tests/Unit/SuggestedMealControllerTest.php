<?php

namespace Tests\Unit;

use App\Models\Patient;
use App\Models\SuggestedMeal;
use App\Http\Controllers\API\SuggestedMealController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SuggestedMealControllerTest extends TestCase
{
    // use RefreshDatabase;
    // use WithFaker;

    private $suggestedMealController;
    private $patient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->patient = Patient::factory()->create();
        $this->actingAs($this->patient);
        $this->suggestedMealController = new SuggestedMealController();
    }

    public function testIndexReturnsAllMeals()
    {
        SuggestedMeal::factory(5)->create();

        $response = $this->suggestedMealController->index();
        $result = $response->getData();

        $this->assertTrue($result->success);
        $this->assertNull($result->message);
        $this->assertCount(5, $result->meals);
    }

    public function testShowReturnsMealById()
    {
        $meal = SuggestedMeal::factory()->create();

        $response = $this->suggestedMealController->show($meal->id);
        $result = $response->getData();

        $this->assertTrue($result->success);
        $this->assertNull($result->message);
        $this->assertEquals($meal->name, $result->data->name);
        $this->assertEquals($meal->details, $result->data->details);
        $this->assertEquals($meal->calories, $result->data->calories);
        $this->assertEquals($meal->protein, $result->data->protein);
        $this->assertEquals($meal->fats, $result->data->fats);
        $this->assertEquals($meal->carbs, $result->data->carbs);
        $this->assertEquals($meal->image, $result->data->image);
    }

    public function testShowReturnsNotFoundForInvalidMealId()
    {
        $mealId = 9999;
        $response = $this->suggestedMealController->show($mealId);
        $result = $response->getData();

        $this->assertFalse($result->success);
        $this->assertNotNull($result->message);
        $this->assertEquals(["You are not authorized to access this information."], $result->message->id);
    }
}
