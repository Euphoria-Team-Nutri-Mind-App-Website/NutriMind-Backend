<?php

namespace App\Http\Controllers\API\Patient;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\Calories\CaloriesRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{

    private $caloriesRepository;

    public function __construct(CaloriesRepositoryInterface $caloriesRepository)
    {
        $this->caloriesRepository = $caloriesRepository;
    }


    // display some doctors in user home page
    public function index(){
        $doctors = Doctor::select('name','image',
        'qualification','rate')->paginate(8);
        return response([
            'status' => true,
            'doctor_info' => $doctors
        ]);

    }

    //display specific doctor information page when patient click on doctor profile
    public function show($id){
        $doctor = Doctor::select('name','phone','gender','image',
        'qualification','experience_years','rate','price')->findOrFail($id);
        return response([
            'status' => true,
            'doctor_info' => $doctor
        ]);
    }

    //Search for specific doctor page in search box
    public function search(Request $request){
        $filter = $request->name;
        $doctor = Doctor::select('name','image',
        'qualification','rate')
            ->where('name', 'LIKE', "%{$filter}%")
            ->get();
        return response([
            'status' => true,
            $doctor
        ]);
    }

    //take patient height and weight then calculate user calories
    public function calculate(){
        return $this->caloriesRepository->calculate();
    }

    // Recommend calories patient need to loos or gain weight
    public function recommendedCalories(){
        return $this->caloriesRepository->recommendedCalories();
    }

    //track patient weight
    public function trackWeight(Request $request){
        $patient = Patient::find(Auth::user()->id);
        $first_weight = $patient->first_weight;
        $current_weight = $request->input('current_weight');
        $calories = $patient->calories;

        $patient->current_weight = $current_weight;
        $patient->save();

        if($first_weight>$current_weight){
            $lost_weight = $first_weight - $current_weight;
        }
        else{
            $lost_weight = $current_weight - $first_weight;
        }

        return response([
            'status' => true,
            'first_weight' => $first_weight .' '. 'Kg',
            'current_weight' => $current_weight .' ' . 'Kg',
            'change in weight' => $lost_weight .' '. 'Kg',
            'your calories' => $calories .' ' .'calorie',
        ]);
    }
}
