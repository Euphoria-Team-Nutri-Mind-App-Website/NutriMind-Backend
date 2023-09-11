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

    public function rules(): array
    {
        $appointmentId = $this->route('appointment') ? $this->route('appointment')->id : null;

        return [
            'full_name' => 'required|string|max:255',
            'age' => 'required|integer',
            'doctor_set_time_id' => [
                'required',
                'integer',
                'exists:doctor_set_times,id',
                $appointmentId ? Rule::unique('appointments', 'doctor_set_time_id')->ignore($appointmentId) : 'unique:appointments,doctor_set_time_id'
            ],
            'doctor_id' => 'required|integer|exists:doctors,id',
            'payment_method' => 'required|string',
            'stripe_id' => 'nullable|integer|required_without:vodafone_cash_id',
            'vodafone_cash_id' => 'nullable|integer|required_without:stripe_id',
            'patient_id' => 'required|integer|exists:patients,id'
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'The full name field is required. Please enter your full name.',
            'full_name.string' => 'The full name field must be a string. Please enter a valid full name.',
            'full_name.max' => 'The full name field must be less than or equal to 255 characters. Please enter a shorter full name.',
            'age.required' => 'The age field is required. Please enter your age.',
            'age.integer' => 'The age field must be an integer. Please enter a valid age.',
            'doctor_set_time_id.required' => 'You should choose a time for that appointment.',
            'doctor_set_time_id.integer' => 'You are not authorized to access this information.',
            'doctor_set_time_id.exists' => 'You are not authorized to access this information.',
            'doctor_set_time_id.unique' => 'That time is not available. Please choose a different time.',
            'doctor_id.required' => 'Please choose the doctor you want to see.',
            'doctor_id.integer' => 'You are not authorized to access this information.',
            'doctor_id.exists' => 'You are not authorized to access this information.',
            'payment_method.required' => 'The payment method field is required. Please enter a payment method.',
            'payment_method.string' => 'The payment method field must be a string. Please enter a valid payment method.',
            'stripe_id.integer' => 'The Stripe ID field must be an integer. Please enter a valid Stripe ID.',
            'stripe_id.required_without' => 'Either the Stripe ID or Vodafone Cash ID must be set.',
            'vodafone_cash_id.integer' => 'The Vodafone Cash ID field must be an integer. Please enter a valid Vodafone Cash ID.',
            'vodafone_cash_id.required_without' => 'Either the Stripe ID or Vodafone Cash ID must be set.',
            'patient_id.required' => 'Please select a patient.',
            'patient_id.integer' => 'You are not authorized to access this information.',
            'patient_id.exists' => 'You are not authorized to access this information.',
        ];
    }
}
