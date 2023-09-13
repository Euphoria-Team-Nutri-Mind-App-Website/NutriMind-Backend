<?php

namespace App\Models;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;
    protected $fillable=['body','patient_id'];

    public function patient(){
        return  $this->belongsTo(Patient::class);
    }
}
