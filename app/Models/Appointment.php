<?php

namespace App\Models;

use App\Models\Doctor;
use App\Models\DoctorSetTime;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable=['full_name','doctor_set_time_id','doctor_id','patient_id','status','payment_method','vodafone_cash_id','stripe_id'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
    public function doctorSetTime(): BelongsTo
    {
        return $this->belongsTo(DoctorSetTime::class);
    }
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
    public function report(): HasOne
    {
        return $this->hasOne(Report::class);
    }
}
