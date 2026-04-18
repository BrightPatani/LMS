<?php

use App\Http\Controllers\Api\V1\AssignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {

    // only instructors can manage assignments, but both students and instructors can view them.
    Route::get('/', [AssignmentController::class, 'index']); // 
    Route::get('/{assignment}', [AssignmentController::class, 'show']);

    // only instructors can create, update, and delete assignments
    Route::middleware('is_instructor')->group(function () {
        Route::post('/', [AssignmentController::class, 'store']);
    });

    // students submit to an assignment
    Route::middleware('is_student')->group(function () {
        Route::post('/{assignment}/submit', [AssignmentController::class, 'submit']);
    });
});