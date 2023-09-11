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
            'full_name' => 'required',
            'patient_id' => 'required|exists:patients,id',
            'payment_method' => 'required|in:stripe,vodafone_cash',
            'stripe_id' => 'nullable|required_if:payment_method,stripe',
            'vodafone_cash_id' => 'nullable|required_if:payment_method,vodafone_cash',
            'age' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'date.required' => 'The date field is required.',
            'date.date_format' => 'The date field must be in the format: Y-m-d.',
            'time.required' => 'The time field is required.',
            'time.date_format' => 'The time field must be in the format: H:i.',
            'doctor_id.required' => 'The doctor ID field is required.',
            'doctor_id.exists' => 'The selected doctor is invalid.',
            'full_name.required' => 'The full name field is required.',
            'patient_id.required' => 'The patient ID field is required.',
            'patient_id.exists' => 'The selected patient is invalid.',
            'payment_method.required' => 'The payment method field is required.',
            'payment_method.in' => 'The selected payment method is invalid.',
            'stripe_id.required_if' => 'The Stripe ID field is required when the payment method is Stripe.',
            'vodafone_cash_id.required_if' => 'The Vodafone Cash ID field is required when the payment method is Vodafone Cash.',
            'age.required' => 'The age field is required.',
            'age.numeric' => 'The age must be a number.',
        ];
    }
}
