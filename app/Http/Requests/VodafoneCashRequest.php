<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VodafoneCashRequest extends FormRequest
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
            'price' => 'required|integer',
            'receipt_image' => 'required|mimes:jpeg,png,jpg,gif|max:2048',
            'patient_phone_number' => ['required', 'digits:11', 'regex:/^(010|011|012|015)[0-9]{8}$/'],
            'doctor_phone_number' => ['required', 'digits:11', 'regex:/^(010|011|012|015)[0-9]{8}$/'],
            'email' => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            'price.*' => 'You are not authorized to access this information.',
            'receipt_image.required' => 'Please upload the receipt image.',
            'receipt_image.mimes' => 'Only JPEG, PNG, JPG, and GIF images are allowed.',
            'receipt_image.max' => 'The uploaded image cannot exceed 2MB in size.',
            'patient_phone_number.required' => 'The patient phone number is required.',
            'patient_phone_number.digits' => 'The patient phone number must be 11 digits.',
            'patient_phone_number.regex' => 'The patient phone number must be an Egyptian number.',
            'doctor_phone_number.*' => 'You are not authorized to access this information.',
            'email.required' => 'The email is required.',
            'email.email' => 'Please enter a valid email address.',
        ];
    }
}
