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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    use GeneralTrait;

    // my scheduale
    public function index()
    {
        date_default_timezone_set('UTC');
        $date = date('Y-m-d');

        $appointments = Appointment::join('doctor_set_times', 'appointments.doctor_set_time_id', '=', 'doctor_set_times.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->leftJoin('reports', 'appointments.id', '=', 'reports.appointment_id')
            ->where('appointments.doctor_id', Auth()->user()->id)
            ->where('date', $date)
            ->where('appointments.status', 'Active')
            ->orderBy('time')
            ->get([
                'full_name',
                'time',
                'diagnosis_of_his_state',
                'description',
                'patients.id'
            ]);
        return $this->returnData('appointments', $appointments);
    }

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

    public function doctor_set_times(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $requestedDate = $request->date;
        $currentDate = Carbon::now()->format('Y-m-d');

        if ($requestedDate > $currentDate) {
            $currentTime = Carbon::now()->format('H:i:s');

            $doctorSetTimes = DoctorsetTime::where('doctor_id', $request->doctor_id)
                ->where('date', $requestedDate)
                ->where('status', 'set')
                ->whereTime('time', '>', $currentTime)
                ->get();
        } else {
            $doctorSetTimes = DoctorsetTime::where('doctor_id', $request->doctor_id)
                ->where('date', $requestedDate)
                ->where('status', 'set')
                ->get();
        }

        return $this->returnData('doctorSetTimes', $doctorSetTimes);
    }


    // book an appointment
    public function store(AppointmentRequest $request)
    {
        $validated = $request->validated();

        $doctorsetTime = DoctorsetTime::create($request->only(['date', 'time', 'doctor_id']));

        $appointmentData = $request->only(['full_name', 'doctor_id', 'payment_method']);
        $appointmentData['doctor_set_time_id'] = $doctorsetTime->id;
        $appointmentData['patient_id'] = Auth()->user()->id;

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

    public function patient_info(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|exists:appointments,id',
            'patient_id' => 'required|exists:patients,id',
            'time' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $info = [];
        $info['time'] = $request->time;
        $info['appointment_id'] = $request->appointment_id;

        $patientInfo = Appointment::join('doctor_set_times', 'appointments.doctor_set_time_id', '=', 'doctor_set_times.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->leftjoin('reports', 'appointments.id', '=', 'reports.appointment_id')
            ->where('patients.id', $request->patient_id)
            ->where('appointments.id', '<>', $request->appointment_id)
            ->orderByDesc('date')
            ->first([
                'full_name',
                'age',
                'gender',
                'diagnosis_of_his_state',
                'description',
            ]);

        if (isset($patientInfo))
            $info = array_merge($info, $patientInfo->toArray());

        return $this->returnData('info', $info);
    }
}
