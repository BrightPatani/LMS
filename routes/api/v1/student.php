<?php

use App\Http\Controllers\Api\V1\Student\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function () {
    //dashboard
    Route::get('/dashboard', [StudentController::class, 'dashboard']);

    //lesson progress
    Route::post('/lessons/{lesson}/complete', [StudentController::class, 'completeLesson']);
    Route::get('/progress/{courseId}', [StudentController::class, 'courseProgress']);

    // my submissions and grades
    Route::get('/submissions', [StudentController::class, 'Submissions']);
    Route::get('/submissions/{id}', [StudentController::class, 'showSubmission']);

    // profile management
    Route::put('/profile', [StudentController::class, 'updateProfile']);
    Route::put('/password', [StudentController::class, 'updatePassword']);
});