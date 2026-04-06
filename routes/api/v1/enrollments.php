<?php

use App\Http\Controllers\Api\V1\EnrollmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // View user's enrollments
    Route::get('/enrollments/my-courses', [EnrollmentController::class, 'myCourses']);
    Route::get('/enrollments/{enrollment}', [EnrollmentController::class, 'show']);

    // Students can enroll and unenroll from courses
    Route::middleware('is_student')->group(function () {
        Route::post('/enrollments', [EnrollmentController::class, 'enroll']);
        Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy']);
    });
});