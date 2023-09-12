<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Models\Appointment;
use App\Models\Report;
use App\Traits\GeneralTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    use GeneralTrait;

    public function store(ReportRequest $request)
    {
        $validated = $request->validated();
        $appointmentId = $validated['appointment_id'];

        $report = Report::where('appointment_id', $appointmentId)->first();

        if ($report) {
            // Update the existing report
            $report->update($validated);
            return $this->returnSuccess('Report updated successfully.');
        }

        // Create a new report
        Report::create($validated);
        return $this->returnSuccess('Report added successfully.');
    }

    public function update(ReportRequest $request, $reportId)
    {
        $validated = $request->validated();
        $report = Report::find($reportId);

        if (!$report) {
            return $this->returnError('Report not found.');
        }

        $report->update($request->only(['diagnosis_of_his_state', 'description']));
        return $this->returnSuccess('Report updated successfully.');
    }

    public function show($appointmentId)
    {
        $validator = Validator::make(['id' => $appointmentId], [
            'id' => 'required|integer',
        ], [
            'id.*' => 'You are not authorized to access this information.',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        $report = Appointment::join('doctor_set_times', 'appointments.doctor_set_time_id', '=', 'doctor_set_times.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->leftJoin('reports', 'appointments.id', '=', 'reports.appointment_id')
            ->orderByDesc('date')
            ->where('appointments.id', $appointmentId)
            ->first([
                'full_name',
                'age',
                'diagnosis_of_his_state',
                'description',
                'appointment_id',
            ]);

        if (!$report) {
            return $this->returnError('Report not found.');
        }

        return $this->returnData('report', $report);
    }

    public function view_report()
    {
        $report = Appointment::join('doctor_set_times', 'appointments.doctor_set_time_id', '=', 'doctor_set_times.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('reports', 'appointments.id', '=', 'reports.appointment_id')
            ->orderByDesc('date')
            ->where('patients.id', Auth()->user()->id)
            ->first([
                'full_name',
                'age',
                'diagnosis_of_his_state',
                'description',
                'appointment_id',
            ]);

        if (!$report) {
            return $this->returnError('Report not found.');
        }

        return $this->returnData('report', $report);
    }
}
