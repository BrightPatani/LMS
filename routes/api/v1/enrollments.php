<?php

use App\Http\Controllers\Api\V1\EnrollmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    // View user's enrollments
    Route::get('/my-courses', [EnrollmentController::class, 'myCourses']);
    Route::get('/{enrollment}', [EnrollmentController::class, 'show']);

    // Students can enroll and unenroll from courses
    Route::middleware('is_student')->group(function () {
        Route::post('/enrollments', [EnrollmentController::class, 'enroll']); // This route allows authenticated students to enroll in a course by calling the 'enroll' method of the EnrollmentController.
        Route::delete('/{enrollment}', [EnrollmentController::class, 'destroy']);
    });
});