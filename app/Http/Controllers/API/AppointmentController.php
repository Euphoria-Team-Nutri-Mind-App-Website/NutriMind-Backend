<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\DoctorsetTime;
use App\Models\DoctorWorkDay;
use App\Models\Patient;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    use GeneralTrait;

    // my scheduale
    public function index(Request $request)
    {
        $validatedMessage = $this->verificationId($request->doctor_id, 'doctors', 'id');
        if (isset($validatedMessage)) {
            return $validatedMessage;
        }
        date_default_timezone_set('UTC');
        $date = date('Y-m-d');

        $appointments = Appointment::join('doctor_set_times', 'appointments.doctor_set_time_id', '=', 'doctor_set_times.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->leftJoin('reports', 'appointments.id', '=', 'reports.appointment_id')
            ->where('appointments.doctor_id', $request->doctor_id)
            ->where('date', $date)
            ->where('appointments.status', 'Active')
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

    // request contains only doctor id and patient id from auth
    public function doctor_work_days_time($doctorId)
    {
        $validatedMessage = $this->verificationId($doctorId, 'doctors', 'id');
        if (isset($validatedMessage)) {
            return $validatedMessage;
        }

        $doctorWork = DoctorWorkDay::where('doctor_id', $doctorId);
        if (isset($doctorWork) && !empty($doctorWork)) {
            $doctorWorkDays = json_decode($doctorWork->get('work_days'), true);
            if (isset($doctorWorkDays)) {
                $doctorWorkDays = array_column($doctorWorkDays, 'work_days');
            } else {
                $doctorWorkDays = [];
            }

            $timeJson = $doctorWork->first('from_to');
            $timeData = json_decode($timeJson, true);

            if (isset($timeData['from_to'])) {
                $time = $timeData['from_to'];
                [$startTime, $finishTime] = explode(' to ', $time);

                // Convert start time to 24-hour format
                $startTime = Carbon::parse($startTime)->format('H:i');

                // Convert finish time to 24-hour format
                $finishTime = Carbon::parse($finishTime)->format('H:i');
            } else {
                $startTime = '';
                $finishTime = '';
            }

            $data = [
                'doctorWorkdays' => $doctorWorkDays,
                'startTime' => $startTime,
                'finishTime' => $finishTime
            ];
        } else {
            $data = [];
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }


    // book an appointment
    public function store(AppointmentRequest $request)
    {
        $validated = $request->validated();

        $doctorsetTime = DoctorsetTime::create($request->only(['date', 'time', 'doctor_id']));

        $appointmentData = $request->only(['full_name', 'doctor_id', 'patient_id', 'payment_method']);
        $appointmentData['doctor_set_time_id'] = $doctorsetTime->id;

        if ($request->has('stripe_id')) {
            $appointmentData['stripe_id'] = $request->stripe_id;
        }

        if ($request->has('vodafone_cash_id')) {
            $appointmentData['vodafone_cash_id'] = $request->vodafone_cash_id;
        }

        Appointment::create($appointmentData);

        Patient::where('id', $request->patient_id)->update([
            'age' => $request->age
        ]);

        return $this->returnSuccess('Appointment added successfully.');
    }

    public function patient_info($patientId)
    {
        $validatedMessage = $this->verificationId($patientId, 'patients', 'id');
        if (isset($validatedMessage)) {
            return $validatedMessage;
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
