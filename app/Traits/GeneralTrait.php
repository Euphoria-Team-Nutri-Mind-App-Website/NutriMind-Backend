<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait GeneralTrait
{
    use HelperTrait;
    public function verificationId($id, $tableName, $IdName)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => "required|integer|exists:$tableName,$IdName",
        ], [
            'id.*' => 'You are not authorized to access this information.'
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }
    }

    public function returnError($msg)
    {
        return $this->createResponse(false, $msg);
    }

    public function returnSuccess($msg = "")
    {
        return $this->createResponse(true, $msg);
    }

    public function returnData($key, $value)
    {
        return $this->createResponse(true, null, [$key => $value]);
    }

    public function getData(Request $request, $modelName, $flagDate=false)
    {
        $queryParams = $request->query();
        $validator = Validator::make($queryParams, $this->getValidationRules($flagDate), $this->getValidationMessages($flagDate));

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        $data = $modelName::query();

        if (isset($queryParams['doctor_id']) || isset($queryParams['patient_id'])) {
            if (isset($queryParams['doctor_id'])) {
                $data->where('doctor_id', $queryParams['doctor_id']);
            }

            if (isset($queryParams['patient_id'])) {
                $data->where('patient_id', $queryParams['patient_id']);
            }

            if (isset($queryParams['date'])) {
                $data->where('date', $queryParams['date']);
            }

            $data = $data->get();
            return $this->returnData('data', $data);
        }

        return $this->returnError('You are not authorized to access this information.');
    }

    public function destroyData($dataId, $model, $tableName)
    {
        $validator = Validator::make(['id' => $dataId], [
            'id' => "required|integer|exists:$tableName,id",
        ], [
            'id.*' => 'You are not authorized to access this information.'
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        $data = $model::find($dataId);
        $data->delete();

        $modelName = $this->getModelName($model);
        return $this->returnSuccess("$modelName deleted successfully.");
    }

    public function viewOne($dataId, $model, $tableName, $IdName, $viewOnlyOne = false, $viewOnlyName = '', $GetData = '*')
    {
        $validatedMessage = $this->verificationId($dataId, $tableName, $IdName);

        if (isset($validatedMessage)) {
            return $validatedMessage;
        }

        $data = $model::where($IdName, $dataId);

        if ($viewOnlyOne) {
            $data = $data->first($viewOnlyName);
        } else {
            $data = $data->first($GetData);
        }

        return $this->returnData('data', $data);
    }
}
