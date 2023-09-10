<?php

use App\Http\Controllers\API\DoctorSetTimeController;
use App\Http\Controllers\API\ReportController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\Chat\ChatController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\DoctorWorkTimeController;
use App\Http\Controllers\API\Patient\PatientController;

//--------------------------------Routes for patient app features--------------------------------//
Route::middleware('auth:patient')->group(function () {
    Route::get('/doctors', [PatientController::class, 'index'])->name('display_doctors');
    Route::get('/doctor/{id}', [PatientController::class, 'show'])->name('doctor_profile');
    Route::get('/doctors/search', [PatientController::class, 'search'])->name('find_doctor');
    Route::resource('doctor_set_times', DoctorSetTimeController::class); //Patient chooses session time
    Route::resource('payment', PaymentController::class);
    Route::resource('appointment', AppointmentController::class)->only([ 'store']);;
});
//------------------------------End Routes for patient app features------------------------------//




//--------------------------Routes for Chat between doctor and patient--------------------------//
Route::middleware('auth')->group(function () {
    Route::post('/create-chat', [ChatController::class, 'create']);
    Route::get('/show-chat-messages', [ChatController::class, 'showMessages']);
    Route::get('/show-chats', [ChatController::class, 'showChats']);
    Route::get('/chats/search', [ChatController::class, 'search']);
    // Route::get('/send-notification', [ChatController::class, 'sendMessageNotification']);

});
//--------------------------End Routes for Chat between doctor and patient--------------------------//



//--------------------------------Routes for docotr app features--------------------------------//
Route::middleware('auth:doctor')->group(function () {
    Route::resource('doctor_work_times', DoctorWorkTimeController::class);
    Route::resource('reports', ReportController::class);
    Route::resource('appointment', AppointmentController::class)->except(['store']);
    Route::get('patient_info/{appointment_id}', [AppointmentController::class, 'patient_info']);
});
//------------------------------End Routes for doctor app features------------------------------//

