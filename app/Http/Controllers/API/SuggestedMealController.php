<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuggestedMealRequest;
use App\Models\SuggestedMeal;
use App\Traits\GeneralTrait;
use App\Traits\ImageTrait;

class SuggestedMealController extends Controller
{
    use GeneralTrait;
    use ImageTrait;

    public function index()
    {
        $meals = SuggestedMeal::all(['name', 'details', 'calories', 'protein', 'fats', 'carbs', 'image']);
        return $this->returnData('meals', $meals);
    }

    public function show($mealId)
    {
        return $this->viewOne($mealId, SuggestedMeal::class, 'suggested_meals', 'id', false, '', ['name', 'details', 'calories', 'protein', 'fats', 'carbs', 'image']);
    }

    public function store(SuggestedMealRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'), 'images/suggested_meals');
        }

        $mealData = $request->except('image');
        if (isset($imagePath)) {
            $mealData['image'] = $imagePath;
        }

        $meal = SuggestedMeal::create($mealData);

        return $this->returnSuccess('Meal added successfully.');
    }
}
