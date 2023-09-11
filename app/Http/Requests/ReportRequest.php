<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'diagnosis_of_his_state' => 'required|string|max:255',
            'description' => 'required|string',
            'appointment_id' => 'required|exists:appointments,id',
        ];
    }

    public function messages()
    {
        return [
            'diagnosis_of_his_state.required' => 'Please add the diagnosis of his state.',
            'diagnosis_of_his_state.string' => 'The diagnosis must be a string.',
            'diagnosis_of_his_state.max' => 'The diagnosis must not exceed 255 characters.',
            'description.required' => 'Please add a description.',
            'description.string' => 'The description must be a string.',
            'appointment_id.*' => 'You are not authorized to access that information.',
        ];
    }
}
