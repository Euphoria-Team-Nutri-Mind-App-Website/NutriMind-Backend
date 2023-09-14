<?php

use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\VodafoneCashController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Chat\ChatController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\HelpSupportController;




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
    Route::get('appointment',[ AppointmentController::class,'index']);
    Route::get('patient_info', [AppointmentController::class, 'patient_info']);
    Route::get('vodafone_cash',[VodafoneCashController::class,'index']);
    Route::put('accept_appointment/{appointment_id}',[VodafoneCashController::class,'accept']);
    Route::put('reject_appointment/{appointment_id}',[VodafoneCashController::class,'reject']);
});
//------------------------------End Routes for doctor app features------------------------------//
