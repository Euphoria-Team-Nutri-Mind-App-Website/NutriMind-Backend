<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\DoctorsetTime;
use App\Models\Patient;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    use GeneralTrait;

    // my scheduale
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|integer|exists:doctors,id',
            'date' => 'required|date',
        ], [
            'doctor_id.*' => 'You are not authorized to access this information.',
            'date.required' => 'Please selecet the date.',
            'date.date'=>'The date format is incorrect.If you think there as something wrong please connect the admins.'
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        $appointments = Appointment::join('doctor_set_times', 'appointments.doctor_set_time_id', '=', 'doctor_set_times.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->leftJoin('reports', 'appointments.id', '=', 'reports.appointment_id')
            ->where('appointments.doctor_id', $request->doctor_id)
            ->where('date', $request->date)
            ->orderBy('time')
            ->get([
                'full_name',
                'time',
                'diagnosis_of_his_state',
                'description',
                'appointment_id'
            ]);
        return $this->returnData('appointments', $appointments);
    }

    // request contains only doctor id and patient id
    public function create_appointment(Request $request)
    {
        
    }


    // book an appointment
    public function store(AppointmentRequest $request)
    {
        $validated = $request->validated();

        Appointment::create($request->except(['age']));
        DoctorsetTime::where('id', $request->doctor_set_time_id)->update([
            'status' => 'set'
        ]);
        $patinet = Patient::where('id', $request->patient_id);
        $patinet->update([
            'age' => $request->age
        ]);

        return $this->returnSuccess('Appointemnt added successfully.');
    }

    public function patient_info($patientId)
    {
        $validator = Validator::make(['id' => $patientId], [
            'id' => "integer",
        ], [
            'id.integer' => 'You are not authorized to access this information.'
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        $info = Appointment::join('doctor_set_times', 'appointments.doctor_set_time_id', '=', 'doctor_set_times.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('reports', 'appointments.id', '=', 'reports.appointment_id')
            ->where('patients.id', $patientId)
            ->orderByDesc('time')
            ->first([
                'full_name',
                'time',
                'age',
                'gender',
                'diagnosis_of_his_state',
                'description',
                'appointment_id'
            ]);
        return $this->returnData('info', $info);
    }
}
