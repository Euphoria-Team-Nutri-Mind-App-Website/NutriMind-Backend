<?php
namespace App\Repository\Calories;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\Calories\CaloriesRepositoryInterface;

class CaloriesRepository implements CaloriesRepositoryInterface
{
    public function calculate(){

        $patient = Patient::find(Auth::user()->id);
        $height = $patient->height;
        $first_weight = $patient->first_weight;
        $current_weight = $patient->current_weight;
        $age = $patient->age;
        $gender = $patient->gender;
        $active_status = $patient->active_status;

        // Check active status
        if ($active_status == 'Idle') {
            $activity_state = 1.2;
        } elseif ($active_status == 'Slack') {
            $activity_state = 1.375;
        } elseif ($active_status == 'Active sometimes') {
            $activity_state = 1.55;
        } else {
            $activity_state = 1.725;
        }

        if($current_weight !=null){
            // Calculate calories
            if ($gender == 'male') {
                $calories = (($current_weight * 10) + ($height * 6.25) - ($age * 5) + 5) * $activity_state;
            } else {
                $calories = (($current_weight * 10) + ($height * 6.25) - ($age * 5) - 161) * $activity_state;
            }
        }else
        {
            $current_weight = $first_weight;
            // Calculate calories
            if ($gender == 'male') {
                $calories = (($current_weight * 10) + ($height * 6.25) - ($age * 5) + 5) * $activity_state;
            } else {
                $calories = (($current_weight * 10) + ($height * 6.25) - ($age * 5) - 161) * $activity_state;
            }
        }

        $patient->calories = $calories;
        $patient->save();

        return response([
            'status' => true,
            'gender' => $gender,
            'age' => $age,
            'active_status' => $active_status,
            'height' => $height,
            'weight' => $current_weight,
            'calories' => (int) $calories,
        ]);
    }

    public function recommendedCalories(){
        $this->calculate();
        $calories= Patient::where('id',Auth::user()->id)->value('calories');
        $active_status= Patient::where('id',Auth::user()->id)->value('active_status');
        return response([
            'Your Active Status' => $active_status,
            'Your calories' => (int) $calories,
            'Lose 0.5 Kg'=>'You need'.' '.(int) $calories - 500 . ' ' .'calories per day to lose 0.5 Kg each week',
            'Lose 1 Kg'=>'You need'.' '. (int) $calories - 1000 . ' ' .'calories per day to lose 1 Kg each week',
            'Gain 0.5 kg'=>'You need'.' '. (int) $calories + 500 . ' ' .'calories per day to gain 0.5 Kg each week',
            'Gain 1 Kg'=>'You need'.' '. (int) $calories + 1000 . ' ' .'calories per day to gain 1 Kg each week',
        ]);
    }

}
