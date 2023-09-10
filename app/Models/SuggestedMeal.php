<?php

namespace App\Models;

use App\Models\PatientSavedMeal;
use App\Models\PatientSelectedMeal;
use App\Models\PatientSuggestedMeal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuggestedMeal extends Model
{
    use HasFactory;
    protected $fillable=['name','calories','protein','fats','carbs','image','details'];

    public function patinetSuggestedMeals(): HasMany
    {
        return $this->hasMany(PatientSuggestedMeal::class);
    }
    public function patinetSavedMeals(): HasMany
    {
        return $this->hasMany(PatientSavedMeal::class);
    }
    public function patinetSelectedMeals(): HasMany
    {
        return $this->hasMany(PatientSelectedMeal::class);
    }

}
