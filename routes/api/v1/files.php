<?php

use App\Http\Controllers\Api\V1\FileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('is_instructor')->group(function () {
        Route::post('/lessons/{lesson}/files', [FileController::class, 'uploadLessonFile']);
        Route::put('/files/{file}', [FileController::class, 'destory']);
    });
});