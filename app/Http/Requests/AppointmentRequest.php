<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'doctor_id' => 'required|exists:doctors,id',
            'full_name' => ['required', 'regex:/^[a-zA-Z\s]+$/', 'min:10', 'max:255'],
            'payment_method' => 'required|in:stripe,vodafone_cash',
            'stripe_id' => [
                'nullable',
                'required_if:payment_method,stripe',
                Rule::unique('appointments', 'stripe_id')->where(function ($query) {
                    return $query->where('payment_method', 'stripe');
                }),
            ],
            'vodafone_cash_id' => [
                'nullable',
                'required_if:payment_method,vodafone_cash',
                Rule::unique('appointments', 'vodafone_cash_id')->where(function ($query) {
                    return $query->where('payment_method', 'vodafone_cash');
                }),
            ],
            'age' => 'required|numeric',
            'doctorWorkDays' => 'required|array',
            'startTime' => 'required|date_format:H:i',
            'finishTime' => 'required|date_format:H:i|after:startTime',
        ];
    }

    public function messages()
    {
        return [
            'date.required' => 'Please enter the date.',
            'date.date_format' => 'The date must be in the format: Y-m-d.',
            'time.required' => 'Please enter the time.',
            'time.date_format' => 'The time must be in the format: H:i.',
            'doctor_id.*' => 'You are not authorized to access that information.',
            'full_name.required' => 'Please enter your name.',
            'full_name.regex' => 'The name must contain only letters and spaces.',
            'full_name.min' => 'The name must be at least 10 characters.',
            'full_name.max' => 'The name cannot exceed 255 characters.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'The selected payment method is invalid.',
            'stripe_id.required_if' => 'Please complete the payment first.',
            'stripe_id.unique' => 'You have already submitted that payment information.',
            'vodafone_cash_id.required_if' => 'Please complete the payment first.',
            'vodafone_cash_id.unique' => 'You have already submitted that payment information.',
            'age.required' => 'Please enter your age.',
            'age.numeric' => 'The age must be a number.',
            'doctorWorkDays.*' => 'You are not authorized to access that information.',
            'startTime.*' => 'You are not authorized to access that information.',
            'finishTime.*' => 'You are not authorized to access that information.',
        ];
    }
}
