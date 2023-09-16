<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\DoctorSetTime;
use App\Models\DoctorWorkDay;
use App\Models\Patient;
use Illuminate\Support\Facades\Date;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    use GeneralTrait;

    private function appointment()
    {
        $today = now()->toDateString();

        $appointments = Appointment::join('doctor_set_times', 'appointments.doctor_set_time_id', '=', 'doctor_set_times.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->leftJoin('reports', 'appointments.id', '=', 'reports.appointment_id')
            ->where('appointments.doctor_id', Auth()->user()->id)
            ->where('appointments.status', 'Active')
            ->whereDate('time', $today) // Filter by today's date
            ->orderBy('time')
            ->get([
                'full_name',
                'time',
                'diagnosis_of_his_state',
                'description',
                'patients.id as patient_id',
                'appointments.id as appointment_id'
            ]);
        return $appointments;
    }

    public function index()
    {
        $appointments = $this->appointment();// array of objects
        if (count($appointments) === 0) {
                Appointment::factory(5)->create([
                    'doctor_id' => Auth()->user()->id,
                    'doctor_set_time_id' => function () {
                        return DoctorSetTime::factory()->create([
                            'doctor_id' => Auth()->user()->id
                        ])->id;
                    },
                ]);
            $appointments = $this->appointment();
            return $appointments;
        }
        return $this->returnData('appointments', $appointments);
    }
    public function doctor_work_days_time($doctorId)
    {
        $validatedMessage = $this->verificationId($doctorId, 'doctors', 'id');
        if (isset($validatedMessage)) {
            return $validatedMessage;
        }

        $doctorWork = DoctorWorkDay::where('doctor_id', $doctorId)->get();

        $doctorWorkDays = [];
        $startTime = '';
        $finishTime = '';

        if ($doctorWork->isNotEmpty()) {
            foreach ($doctorWork as $work) {
                $doctorWorkDays[] = $work->work_days;
            }

            $timeData = $doctorWork[0]->from_to;
            $timeParts = explode(' to ', $timeData);
            $startTime = isset($timeParts[0]) ? Carbon::parse($timeParts[0])->format('H:i') : '';
            $finishTime = isset($timeParts[1]) ? Carbon::parse($timeParts[1])->format('H:i') : '';
        }

        $data = [
            'doctorWorkdays' => $doctorWorkDays,
            'startTime' => $startTime,
            'finishTime' => $finishTime
        ];

        return $this->returnData('data', $data);
    }

    public function doctor_set_times(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        $requestedDate = $request->date;

        $query = DoctorSetTime::where('doctor_id', $request->doctor_id)
            ->where('date', $requestedDate)
            ->where('status', 'set');

        $DoctorSetTimes = $query->get();

        return $this->returnData('DoctorSetTimes', $DoctorSetTimes);
    }
    public function store(AppointmentRequest $request)
    {
        $validated = $request->validated();

        $doctorWorkDays = $request->doctorWorkDays; // array
        $startTime = $request->startTime;
        $finishTime = $request->finishTime;

        $date = $request->date;
        $time = $request->time;

        $dayName = date('l', strtotime($date));

        // Check if the selected day is a valid work day for the doctor
        if (!in_array($dayName, $doctorWorkDays)) {
            return $this->returnError('The selected date is not available for the doctor.');
        }

        // Check if the appointment time is within the working hours
        if ($time < $startTime || $time > $finishTime) {
            return $this->returnError('The selected time is not available for the doctor.');
        }

        $doctorSetTime = DoctorSetTime::create($request->only(['date', 'time', 'doctor_id']));

        $appointmentData = $request->only(['full_name', 'doctor_id', 'payment_method']);
        $appointmentData['doctor_set_time_id'] = $doctorSetTime->id;
        $appointmentData['patient_id'] = auth()->user()->id;

        if ($request->has('stripe_id')) {
            $appointmentData['stripe_id'] = $request->stripe_id;
            $appointmentData['status'] = "Active";
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
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        $info = $request->only('appointment_id');

        $patientInfo = Appointment::join('doctor_set_times', 'appointments.doctor_set_time_id', '=', 'doctor_set_times.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->leftJoin('reports', 'appointments.id', '=', 'reports.appointment_id')
            ->where('patients.id', $request->patient_id)
            ->where('appointments.id', '<>', $request->appointment_id)
            ->where('appointments.doctor_id', Auth()->user()->id)
            ->orderByDesc('date')
            ->first([
                'full_name',
                'time',
                'age',
                'gender',
                'diagnosis_of_his_state',
                'description',
            ]);

        if ($patientInfo) {
            $info = array_merge($info, $patientInfo->toArray());
        }

        return $this->returnData('info', $info);
    }
}
