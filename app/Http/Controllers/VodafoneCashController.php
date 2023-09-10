<?php

namespace App\Http\Controllers;

use App\Http\Requests\VodafoneCashRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSetTime;
use App\Models\VodafoneCash;
use App\Traits\GeneralTrait;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VodafoneCashController extends Controller
{
    use GeneralTrait,ImageTrait;

    // show list for doctor
    public function index()
    {
        $doctor_id=Auth()->user()->id;
        $data=VodafoneCash::leftjoin('appointments','vodafone_cashes.id','=','payments.id')
        ->where('doctor_id',$doctor_id)
        ->where('payment_method','vodafone_cash')
        ->get(['price','status','full_name','patient_phone_number','receipt_image','appointments.id']);
        return $this->returnData('data',$data);
    }

    // steps of vodafone cash
    public function view_payment_steps($doctorId)
    {
        $validatedMessage = $this->verificationId($doctorId, 'doctors', 'id');
        if (isset($validatedMessage)) {
            return $validatedMessage;
        }

        $data=Doctor::where('id',$doctorId)->get([
            'name',
            'rate',
            'qualification',
            'rate',
            'price',
            'image',
            'vodafone_cash'
        ]);
        return $this->returnData('data',$data);
    }

    public function store(VodafoneCashRequest $request)
    {
        $validated=$request->validated();
        $vodafoneCash=VodafoneCash::create($request->all());

        if ($request->hasFile('receipt_image')) {
            $imagePath = $this->uploadImage($request->file('receipt_image'), 'images/receipts');
        }

        if (isset($imagePath)) {
            $vodafoneCash->update([
                'receipt_image' => $imagePath,
            ]);
        }
        return $this->returnData('PaymentId',$vodafoneCash->id);
    }
    public function accept($appointmentId)
    {
        $validatedMessage = $this->verificationId($appointmentId, 'appointments', 'id');
        if (isset($validatedMessage)) {
            return $validatedMessage;
        }

        Appointment::where('id',$appointmentId)->update([
            'status'=>'Active'
        ]);
        return $this->returnSuccess('APpointment activated successfully.');
    }
    public function reject($appointmentId)
    {
        $validatedMessage = $this->verificationId($appointmentId, 'appointments', 'id');
        if (isset($validatedMessage)) {
            return $validatedMessage;
        }

        Appointment::where('id',$appointmentId)->update([
            'status'=>'Failed'
        ]);
        DoctorSetTime::where('appointment_id',$appointmentId)->update([
            'status'=>'not set'
        ]);

        return $this->returnSuccess('APpointment rejected successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VodafoneCash $vodafoneCash)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VodafoneCash $vodafoneCash)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VodafoneCash $vodafoneCash)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VodafoneCash $vodafoneCash)
    {
        //
    }
}
