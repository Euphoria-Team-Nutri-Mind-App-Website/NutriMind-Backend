<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\VodafoneCashRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSetTime;
use App\Models\VodafoneCash;
use App\Traits\GeneralTrait;
use App\Traits\ImageTrait;

class VodafoneCashController extends Controller
{
    use GeneralTrait, ImageTrait;

    // show list for doctor
    public function index()
    {
        $doctor_id = Auth()->user()->id;
        $data = VodafoneCash::leftjoin('appointments', 'vodafone_cashes.id', '=', 'payments.id')
            ->where('doctor_id', $doctor_id)
            ->where('payment_method', 'vodafone_cash')
            ->get(['price', 'status', 'full_name', 'patient_phone_number', 'receipt_image', 'appointments.id']);
        return $this->returnData('data', $data);
    }

    // steps of vodafone cash
    public function view_payment_steps($doctorId)
    {
        $validatedMessage = $this->verificationId($doctorId, 'doctors', 'id');
        if (isset($validatedMessage)) {
            return $validatedMessage;
        }

        $data = Doctor::where('id', $doctorId)->get([
            'name',
            'rate',
            'qualification',
            'rate',
            'price',
            'image',
            'vodafone_cash'
        ]);
        return $this->returnData('data', $data);
    }

    public function store(VodafoneCashRequest $request)
    {
        $validated = $request->validated();
        $vodafoneCash = VodafoneCash::create($request->all());

        if ($request->hasFile('receipt_image')) {
            $imagePath = $this->uploadImage($request->file('receipt_image'), 'images/receipts');
        }

        if (isset($imagePath)) {
            $vodafoneCash->update([
                'receipt_image' => $imagePath,
            ]);
        }
        return $this->returnData('vodafone_cash_id', $vodafoneCash->id);
    }
    public function accept($appointmentId)
    {
        $validatedMessage = $this->verificationId($appointmentId, 'appointments', 'id');
        if (isset($validatedMessage)) {
            return $validatedMessage;
        }

        Appointment::where('id', $appointmentId)->update([
            'status' => 'Active'
        ]);
        return $this->returnSuccess('Appointment activated successfully.');
    }
    public function reject($appointmentId)
    {
        $validatedMessage = $this->verificationId($appointmentId, 'appointments', 'id');
        if (isset($validatedMessage)) {
            return $validatedMessage;
        }

        Appointment::where('id', $appointmentId)->update([
            'status' => 'Failed'
        ]);
        DoctorSetTime::join('appointments', 'appointments.doctor_set_time_id', '=', 'doctor_set_times.id')
            ->where('appointments.id', $appointmentId)->update([
                    'doctor_set_times.status' => 'not set'
                ]);

        return $this->returnSuccess('Appointment rejected successfully.');
    }
}
