<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VodafoneCash extends Model
{
    use HasFactory;
    protected $fillable=['price','patient_phone_number','doctor_phone_number','receipt_image','email'];
}
