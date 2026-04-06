<?php

use App\Http\Controllers\Api\V1\LessonController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Any authenticated user can view lessons
    Route::get('/courses/{course}/lessons', [LessonController::class, 'index']);
    Route::get('/lessons/{lesson}', [LessonController::class, 'show']);

    // Only instructors can manage lessons
    Route::middleware('is_instructor')->group(function () {
        Route::post('/lessons', [LessonController::class, 'store']);
        Route::put('/lessons/{lesson}', [LessonController::class, 'update']);
        Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy']);
    });
});
