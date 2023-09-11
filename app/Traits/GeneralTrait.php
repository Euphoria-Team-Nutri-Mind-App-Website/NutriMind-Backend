<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait GeneralTrait
{
    public function verificationId($id,$tableName,$IdName)
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
        return response()->json([
            'success' => false,
            'message' => $msg
        ]);
    }
    public function returnSuccess($msg = "")
    {
        return response()->json([
            'success' => true,
            'message' => $msg
        ]);
    }
    public function returnData($key, $value)
    {
        return response()->json([
            'success' => true,
            $key => $value
        ]);
    }

    // i made that because in every controller i need to return data depend on doctor_id or patient_id or both... so i reduce repetation on my code
    public function getData(Request $request, $modelName, $flagDate)
    {
        $queryParams = $request->query();

        // Define custom error messages
        if ($flagDate) {
            $customMessages = [
                'doctor_id.*' => 'You are not authorized to access this information.',
                'patient_id.*' => 'You are not authorized to access this information.',
                'date.required' => 'Please choose the date',
                'date.date' => 'Uncorrect format for date. If you think there is something worng please connect the admins.'
            ];

            // Validate input parameters
            $validator = Validator::make($queryParams, [
                'doctor_id' => 'sometimes|required_without:patient_id|exists:doctors,id',
                'patient_id' => 'sometimes|required_without:doctor_id|exists:patients,id',
                'date' => 'required|date'
            ], $customMessages);
        } else {
            $customMessages = [
                'doctor_id.*' => 'You are not authorized to access this information.',
                'patient_id.*' => 'You are not authorized to access this information.',
            ];

            // Validate input parameters
            $validator = Validator::make($queryParams, [
                'doctor_id' => 'sometimes|required_without:patient_id|exists:doctors,id',
                'patient_id' => 'sometimes|required_without:doctor_id|exists:patients,id',
            ], $customMessages);
        }
        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        $data = $modelName::query();

        // Filter payments by doctor_id or patient_id
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

        if (isset($data->image) && $data->image !== 'images/meals/food.jpg') {
            $this->deleteImage($data->image);
        }

        $data->delete();
        $lastPosition = strrpos($model, DIRECTORY_SEPARATOR);
        $substring = substr($model, $lastPosition + 1);

        // Insert spaces between consecutive capital characters
        $substring = preg_replace('/([a-z])([A-Z])/', '$1 $2', $substring);

        // Convert all characters except the first one to lowercase
        $substring = ucfirst(strtolower($substring));
        return $this->returnSuccess("$substring deleted successfully.");
    }
    public function viewOne($dataId, $model, $tableName, $IdName,$viewOnlyOne=false,$viewOnlyName='',$GetData='*')
    {
        $validatedMessage=$this->verificationId($dataId,$tableName,$IdName);
        if(isset($validatedMessage))
        {
            return $validatedMessage;
        }
        
        $data = $model::where($IdName, $dataId);
        if($viewOnlyOne)
        {
            $data=$data->first($viewOnlyName);
        }
        else{
            $data=$data->first($GetData);
        }
        return $this->returnData('data', $data);
    }
    public function deleteImage($imagePath)
    {
        $publicPath = public_path($imagePath);

        if (file_exists($publicPath)) {
            unlink($publicPath);
        }
    }
}
?>
