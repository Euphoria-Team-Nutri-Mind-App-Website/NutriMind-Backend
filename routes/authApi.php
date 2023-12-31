<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Authentication\DoctorAuthenticationController;
use App\Http\Controllers\API\Authentication\PatientAuthenticationController;
use App\Models\Doctor;

//--------------------------------------------Authentication Routes-----------------------------------------------------//

//-----------------------------Doctor authentication routes-----------------------------//
Route::post('/doctor/register',[DoctorAuthenticationController::class,'register']);
Route::post('/doctor/login',[DoctorAuthenticationController::class,'login']);
Route::get('/doctor/generate-otp',[DoctorAuthenticationController::class,'generateOTP']);
Route::get('/doctor/verify-otp',[DoctorAuthenticationController::class,'verifyOTP']);
Route::post('/doctor/reset-password',[DoctorAuthenticationController::class,'resetPassword']);


//------- Social Network Authentecation Routes--------//
Route::get('/login/{provider}', [DoctorAuthenticationController::class,'redirectToProvider']);
Route::get('/login/{provider}/callback', [DoctorAuthenticationController::class,'handleProviderCallback']);



Route::middleware('auth:doctor')->group(function(){
    Route::post('/doctor/work-days',[DoctorAuthenticationController::class,'setWorkTime']);
    Route::get('/doctor/logout',[DoctorAuthenticationController::class,'logout']);
    Route::get('/doctor/profile',[DoctorAuthenticationController::class,'show']);
    Route::post('/doctor/update-profile',[DoctorAuthenticationController::class,'update']);
    Route::get('/doctor/destroy',[DoctorAuthenticationController::class,'destroy']);
});
//--------------------------------------------------------------------------------------//






//-----------------------------Patient authentication routes-----------------------------//
Route::post('/patient/register',[PatientAuthenticationController::class,'register']);
Route::post('/patient/login',[PatientAuthenticationController::class,'login']);
Route::get('/patient/generate-otp',[PatientAuthenticationController::class,'generateOTP']);
Route::get('/patient/verify-otp',[PatientAuthenticationController::class,'verifyOTP']);
Route::post('/patient/reset-password',[PatientAuthenticationController::class,'resetPassword']);


//------- Social Network Authentecation Routes--------//
Route::get('/login/{provider}', [PatientAuthenticationController::class,'redirectToProvider']);
Route::get('/login/{provider}/callback', [PatientAuthenticationController::class,'handleProviderCallback']);




Route::middleware('auth:patient')->group(function(){
    Route::post('/patient/weight',[PatientAuthenticationController::class,'getWeight']);
    Route::post('/patient/height',[PatientAuthenticationController::class,'getHeight']);
    Route::post('/patient/active-status',[PatientAuthenticationController::class,'getActiveStatus']);
    Route::get('/patient/logout',[PatientAuthenticationController::class,'logout']);
    Route::get('/patient/profile',[PatientAuthenticationController::class,'show']);
    Route::post('/patient/update-profile',[PatientAuthenticationController::class,'update']);
    Route::get('/patient/destroy',[PatientAuthenticationController::class,'destroy']);
});
//--------------------------------------------------------------------------------------//

//--------------------------------------------End Authentication Routes-----------------------------------------------------//
