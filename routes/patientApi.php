<?php


use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\VodafoneCashController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\GameController;
use App\Http\Controllers\API\MealController;
use App\Http\Controllers\API\MoodController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\Patient\QouteController;
use App\Http\Controllers\API\SuggestedMealController;
use App\Http\Controllers\API\Patient\PatientController;
use App\Http\Controllers\API\Patient\QuestionnaireController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\StripeController;

//--------------------------------Routes for patient app features--------------------------------//
Route::middleware('auth:patient')->group(function () {
    Route::get('/doctors', [PatientController::class, 'index'])->name('display_doctors');
    Route::get('/doctor/{id}', [PatientController::class, 'show'])->name('doctor_profile');
    Route::get('/doctors/search', [PatientController::class, 'search'])->name('find_doctor');
    Route::post('/track-weight', [PatientController::class, 'trackWeight']);
    Route::get('doctor_set_times', [AppointmentController::class,'doctor_set_times']); //Patient chooses session time
    Route::get('doctor_work_days_time/{doctor_id}', [AppointmentController::class,'doctor_work_days_time']);
    Route::post('appointment', [AppointmentController::class, 'store']);
    Route::post('vodafone_cash',[VodafoneCashController::class,'store']);
    Route::get('view_report', [ReportController::class,'view_report']);
    Route::get('view_payment_steps/{doctor_id}',[VodafoneCashController::class,'view_payment_steps']);
    Route::resource('reviews',ReviewController::class);
});
//------------------------------End Routes for patient app features------------------------------//




//--------------------------------Routes for patient meals features--------------------------------//
Route::middleware('auth:patient')->group(function () {
    Route::get('/calories', [PatientController::class, 'calculate']);
    Route::get('/recommended-calories', [PatientController::class, 'recommendedCalories']);
    Route::resource('meals', MealController::class);
    Route::resource('suggested_meals', SuggestedMealController::class);
});
//------------------------------End Routes for patient meals features------------------------------//





//--------------------------------Routes for patient notes & qoutes and questions features--------------------------------//
Route::middleware('auth:patient')->group(function () {
    Route::get('/qoutes', [QouteController::class, 'index'])->name('get_qoutes');
    Route::get('/questions', [QuestionnaireController::class, 'show'])->name('display_questions');
    Route::post('/answer/questions', [QuestionnaireController::class, 'answer']);
    Route::resource('notes', NoteController::class); //Patient make notes controller
    Route::get('serch_for_note', [NoteController::class, 'search']);
});
//------------------------------End Routes for patient notes & qoutes and questions features------------------------------//


//--------------------------------Routes for stripe-------------------------------//
Route::/*middleware('auth:patient')->*/name('stripe.')->controller(StripeController::class)->group(function () {
    Route::get('payment', 'index')->name('index');
    Route::post('payment', 'store')->name('store');
});

//------------------------------End Routes for stripe------------------------------//
