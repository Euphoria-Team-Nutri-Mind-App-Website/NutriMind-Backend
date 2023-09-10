<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuggestedMealRequest;
use App\Models\SuggestedMeal;
use App\Traits\GeneralTrait;
use App\Traits\ImageTrait;
use GuzzleHttp\Psr7\Request;

class SuggestedMealController extends Controller
{
    use GeneralTrait;
    use ImageTrait;

    public function index()
    {
        $meals = SuggestedMeal::all(['name','details','calories','protein','fats','carbs','image']);
        return $this->returnData('meals', $meals);
    }
    public function show($mealId)
    {
        return $this->viewOne($mealId,'App\Models\SuggestedMeal','suggested_meals','id',false,'',['name','details','calories','protein','fats','carbs','image']);
    }
    public function store(SuggestedMealRequest $request)
    {
        $validated=$request->validated();
        
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'), 'images/suggested_meals');
        }

        $meal = SuggestedMeal::create($request->all());

        if (isset($imagePath)) {
            $meal->update([
                'image' => $imagePath,
            ]);
        }

        return $this->returnSuccess('Meal added successfully.');
    }
}
