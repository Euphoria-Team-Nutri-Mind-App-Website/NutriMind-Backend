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
use Illuminate\Support\Facades\Auth;

class VodafoneCashController extends Controller
{
    use GeneralTrait, ImageTrait;

    // Show list of Vodafone Cash payments for the current doctor
    public function index()
    {
        $doctorId = Auth::user()->id;
        $data = VodafoneCash::leftJoin('appointments', 'vodafone_cashes.id', '=', 'appointments.id')
            ->where('doctor_id', $doctorId)
            ->where('payment_method', 'vodafone_cash')
            ->get(['price', 'status', 'full_name', 'patient_phone_number', 'receipt_image', 'appointments.id']);
        return $this->returnData('data', $data);
    }

    // Retrieve payment steps for Vodafone Cash
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
            'vodafone_cash',
        ]);
        return $this->returnData('data', $data);
    }

    // Store a new Vodafone Cash payment
    public function store(VodafoneCashRequest $request)
    {
        $validated = $request->validated();

        $imagePath = $this->uploadImage($request->file('receipt_image'), 'images/receipts');

        $vodafoneCashData = $request->except('receipt_image');
        $vodafoneCashData['receipt_image'] = $imagePath;

        $vodafoneCash = VodafoneCash::create($vodafoneCashData);

        return $this->returnData('vodafone_cash_id', $vodafoneCash->id);
    }

    // Accept an appointment
    public function accept($appointmentId)
    {
        $validatedMessage = $this->verificationId($appointmentId, 'appointments', 'id');
        if (isset($validatedMessage)) {
            return $validatedMessage;
        }

        Appointment::where('id', $appointmentId)
            ->where('doctor_id', Auth()->user()->id)
            ->update([
                'status' => 'Active',
            ]);

        return $this->returnSuccess('Appointment activated successfully.');
    }

    // Reject an appointment
    public function reject($appointmentId)
    {
        $validatedMessage = $this->verificationId($appointmentId, 'appointments', 'id');
        if (isset($validatedMessage)) {
            return $validatedMessage;
        }

        Appointment::where('id', $appointmentId)
            ->where('doctor_id', Auth()->user()->id)
            ->update([
                'status' => 'Failed',
            ]);

        DoctorSetTime::join('appointments', 'appointments.doctor_set_time_id', '=', 'doctor_set_times.id')
            ->where('appointments.id', $appointmentId)
            ->update([
                'doctor_set_times.status' => 'not set',
            ]);

        return $this->returnSuccess('Appointment rejected successfully.');
    }
}
