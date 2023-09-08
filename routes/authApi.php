<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Authentication\DoctorAuthenticationController;
use App\Http\Controllers\API\Authentication\PatientAuthenticationController;




//--------------------------------------------Authentication Routes-----------------------------------------------------//

//-----------------------------Doctor authentication routes-----------------------------//
Route::post('/doctor/register',[DoctorAuthenticationController::class,'register']);
Route::post('/doctor/login',[DoctorAuthenticationController::class,'login']);



//------- Social Network Authentecation Routes--------//
Route::get('/login/{provider}', [DoctorAuthenticationController::class,'redirectToProvider']);
Route::get('/login/{provider}/callback', [DoctorAuthenticationController::class,'handleProviderCallback']);



Route::middleware('auth:doctor')->group(function(){
    Route::get('/doctor/logout',[DoctorAuthenticationController::class,'logout']);
    Route::get('/doctor/profile',[DoctorAuthenticationController::class,'show']);
    Route::post('/doctor/update-profile',[DoctorAuthenticationController::class,'update']);
    Route::get('/doctor/destroy',[DoctorAuthenticationController::class,'destroy']);
    Route::post('/doctor/restore-password',[DoctorAuthenticationController::class,'restorePassword']);
});
//--------------------------------------------------------------------------------------//






//-----------------------------Patient authentication routes-----------------------------//
Route::post('/patient/register',[PatientAuthenticationController::class,'register']);
Route::post('/patient/login',[PatientAuthenticationController::class,'login']);



//------- Social Network Authentecation Routes--------//
Route::get('/login/{provider}', [PatientAuthenticationController::class,'redirectToProvider']);
Route::get('/login/{provider}/callback', [PatientAuthenticationController::class,'handleProviderCallback']);




Route::middleware('auth:patient')->group(function(){
    Route::get('/patient/logout',[PatientAuthenticationController::class,'logout']);
    Route::get('/patient/profile',[PatientAuthenticationController::class,'show']);
    Route::post('/patient/update-profile',[PatientAuthenticationController::class,'update']);
    Route::get('/patient/destroy',[PatientAuthenticationController::class,'destroy']);
    Route::post('/patient/reset-password',[PatientAuthenticationController::class,'resetPassword']);
});
//--------------------------------------------------------------------------------------//

//--------------------------------------------End Authentication Routes-----------------------------------------------------//
