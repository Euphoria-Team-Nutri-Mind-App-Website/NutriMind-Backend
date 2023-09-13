<?php
namespace App\Traits;

trait HelperTrait
{
    private function getValidationRules($flagDate)
    {
        $rules = [
            'doctor_id' => 'sometimes|required_without:patient_id|exists:doctors,id',
            'patient_id' => 'sometimes|required_without:doctor_id|exists:patients,id',
        ];

        if ($flagDate) {
            $rules['date'] = 'required|date';
        }

        return $rules;
    }

    private function getValidationMessages($flagDate)
    {
        $messages = [
            'doctor_id.*' => 'You are not authorized to access this information.',
            'patient_id.*' => 'You are not authorized to access this information.',
        ];

        if ($flagDate) {
            $messages['date.required'] = 'Please choose the date';
            $messages['date.date'] = 'Incorrect format for the date. If you think there is something wrong, please contact the admins.';
        }

        return $messages;
    }

    private function createResponse($success, $message = null, $data = null)
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            $response = array_merge($response, $data);
        }

        return response()->json($response);
    }

    private function getModelName($model)
    {
        $lastPosition = strrpos($model, DIRECTORY_SEPARATOR);
        $substring = substr($model, $lastPosition + 1);
        return $substring;
    }
}
