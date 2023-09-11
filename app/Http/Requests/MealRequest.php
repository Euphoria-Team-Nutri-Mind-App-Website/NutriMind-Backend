<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "name" => "required|string|max:255",
            "calories" => "required|integer|min:0",
            "protein" => "required|integer|min:0",
            "fats" => "required|integer|min:0",
            "carbs" => "required|integer|min:0",
            "time" => "required|regex:/^[0-9]{2}:[0-9]{2}$/",
            "date" => "required|regex:/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",
            "type" => "required|in:breakfast,lunch,dinner",
            'image' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
            'patient_id' => 'required|integer|exists:patients,id',
        ];
    }

    public function messages(): array
    {
        return [
            "name.required" => "Please provide the name of the meal.",
            "name.string" => "The name must be a string.",
            "name.max" => "The name cannot exceed 255 characters.",
            "calories.required" => "Please enter the number of calories for the meal.",
            "calories.integer" => "The calories must be a whole number.",
            "calories.min" => "The calories cannot be negative.",
            "protein.required" => "Please enter the amount of protein for the meal.",
            "protein.integer" => "The protein must be a whole number.",
            "protein.min" => "The protein cannot be negative.",
            "fats.required" => "Please enter the amount of fats for the meal.",
            "fats.integer" => "The fats must be a whole number.",
            "fats.min" => "The fats cannot be negative.",
            "carbs.required" => "Please enter the amount of carbs for the meal.",
            "carbs.integer" => "The carbs must be a whole number.",
            "carbs.min" => "The carbs cannot be negative.",
            "time.required" => "Please provide the time for the meal.",
            "time.regex" => "The time must be in the format HH:mm.",
            "date.required" => "Please provide the date for the meal.",
            "date.regex" => "The date must be in the format YYYY-MM-DD.",
            "type.required" => "Please specify the type of the meal.",
            "type.in" => "The meal type must be one of breakfast, lunch, or dinner.",
            "image.mimes" => "Only JPEG, PNG, JPG, and GIF images are allowed.",
            "image.max" => "The uploaded image cannot exceed 2MB in size.",
            'patient_id.required' => 'Please specify the patient for the meal.',
            'patient_id.integer' => 'The patient ID must be an integer.',
            'patient_id.exists' => 'The selected patient is invalid.',
        ];
    }
}
