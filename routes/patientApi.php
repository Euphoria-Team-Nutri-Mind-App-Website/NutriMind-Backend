<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\GameController;
use App\Http\Controllers\API\MealController;
use App\Http\Controllers\API\MoodController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\Patient\QouteController;
use App\Http\Controllers\API\SuggestedMealController;
use App\Http\Controllers\API\Patient\PatientController;
use App\Http\Controllers\API\Patient\QuestionnaireController;


//--------------------------------Routes for patient meals features--------------------------------//
Route::middleware('auth:patient')->group(function () {
    Route::get('/calories', [PatientController::class, 'calculate']);
    Route::get('/recommended-calories', [PatientController::class, 'recommendedCalories']);
    Route::resource('meals', MealController::class);
    Route::resource('suggested_meals', SuggestedMealController::class);
});
//------------------------------End Routes for patient meals features------------------------------//




//--------------------------------Routes for patient game and mode features--------------------------------//
Route::middleware('auth:patient')->group(function () {
    Route::resource('moods', MoodController::class);
    Route::resource('game', GameController::class);
});
//------------------------------End Routes for patient game and mode features------------------------------//




//--------------------------------Routes for patient notes & qoutes and questions features--------------------------------//
Route::middleware('auth:patient')->group(function () {
    Route::get('/qoutes', [QouteController::class, 'index'])->name('get_qoutes');
    Route::get('/questions', [QuestionnaireController::class, 'show'])->name('display_questions');
    Route::post('/answer/questions', [QuestionnaireController::class, 'answer']);
    Route::resource('notes', NoteController::class); //Patient make notes controller
    Route::get('serch_for_note', [NoteController::class, 'search']);
});
//------------------------------End Routes for patient notes & qoutes and questions features------------------------------//




