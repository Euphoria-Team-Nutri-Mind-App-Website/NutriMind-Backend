<?php

use App\Http\Controllers\API\DoctorSetTimeController;
use App\Http\Controllers\API\ReportController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\Chat\ChatController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\DoctorWorkTimeController;
use App\Http\Controllers\API\Patient\PatientController;
use App\Http\Controllers\HelpSupportController;
use App\Http\Controllers\VodafoneCashController;
use App\Models\VodafoneCash;

//--------------------------------Routes for patient app features--------------------------------//
Route::middleware('auth:patient')->group(function () {
    Route::get('/doctors', [PatientController::class, 'index'])->name('display_doctors');
    Route::get('/doctor/{id}', [PatientController::class, 'show'])->name('doctor_profile');
    Route::get('/doctors/search', [PatientController::class, 'search'])->name('find_doctor');
    Route::post('/track-weight', [PatientController::class, 'trackWeight']);
    Route::resource('doctor_set_times', DoctorSetTimeController::class); //Patient chooses session time
    Route::resource('payment', PaymentController::class);
    Route::resource('appointment', AppointmentController::class)->only([ 'store']);
    Route::post('vodafone_cash',[VodafoneCashController::class,'store']);
});
//------------------------------End Routes for patient app features------------------------------//




//--------------------------Routes for Chat between doctor and patient--------------------------//
Route::middleware('auth')->group(function () {
    Route::post('/create-chat', [ChatController::class, 'create']);
    Route::get('/show-chat-messages', [ChatController::class, 'showMessages']);
    Route::get('/show-chats', [ChatController::class, 'showChats']);
    Route::get('/chats/search', [ChatController::class, 'search']);
    Route::post('/help-support', [HelpSupportController::class,'sendEmail'])->name('help-support.send');
    // Route::get('/send-notification', [ChatController::class, 'sendMessageNotification']);

});
//--------------------------End Routes for Chat between doctor and patient--------------------------//



//--------------------------------Routes for doctor app features--------------------------------//
Route::middleware('auth:doctor')->group(function () {
    Route::resource('reports', ReportController::class);
    Route::resource('appointment', AppointmentController::class)->except(['store']);
    Route::get('patient_info/{appointment_id}', [AppointmentController::class, 'patient_info']);
    Route::get('vodafone_cash',[VodafoneCashController::class,'index']);
    Route::get('view_payment_steps/{doctor_id}',[VodafoneCashController::class,'view_payment_steps']);
    Route::put('accept_appointment/{appointment_id}',[VodafoneCashController::class,'accept']);
    Route::put('reject_appointment/{appointment_id}',[VodafoneCashController::class,'reject']);
});
//------------------------------End Routes for doctor app features------------------------------//

Route::get('doctor_work_days_time/{doctor_id}',[AppointmentController::class,'doctor_work_days_time']);
