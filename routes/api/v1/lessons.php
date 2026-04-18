<?php

use App\Http\Controllers\Api\V1\LessonController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    // Any authenticated user can view lessons
    Route::get('/courses/{course}/lessons', [LessonController::class, 'index']);
    Route::get('/{lesson}', [LessonController::class, 'show']);

    // Only instructors can manage lessons
    Route::middleware('is_instructor')->group(function () {
        Route::post('/', [LessonController::class, 'store']);
        Route::put('/{lesson}', [LessonController::class, 'update']);
        Route::delete('/{lesson}', [LessonController::class, 'destroy']);
    });
});
