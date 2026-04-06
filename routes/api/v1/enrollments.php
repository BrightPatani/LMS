<?php

use App\Http\Controllers\Api\V1\EnrollmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'is_student'])->group(function () {
    Route::post('/enrollments', [EnrollmentController::class, 'store']);
    Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy']);
});