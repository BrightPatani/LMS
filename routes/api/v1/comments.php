<?php

use App\Http\Controllers\Api\V1\CommentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    // Get comments for a specific entity (course, lesson, assignment)
    Route::get('/comments', [CommentController::class, 'index']);
    
    // Create a new comment
    Route::post('/comments', [CommentController::class, 'store']);
});