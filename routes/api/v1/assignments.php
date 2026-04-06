<?php

use App\Http\Controllers\Api\V1\AssignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // only instructors can manage assignments, but both students and instructors can view them.
    Route::get('/assignments', [AssignmentController::class, 'index']);
    Route::get('/assignments/{assignment}', [AssignmentController::class, 'show']);

    // only instructors can create, update, and delete assignments
    Route::middleware('is_instructor')->group(function () {
        Route::post('/assignments', [AssignmentController::class, 'store']);
    });

    // students submit to an assignment
    Route::middleware('is_student')->group(function () {
        Route::post('/assignments/{assignment}/submit', [AssignmentController::class, 'submit']);
    });
});