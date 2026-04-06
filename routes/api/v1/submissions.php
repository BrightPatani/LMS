<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Optional: add submission-specific routes here if separate from AssignmentController
    // e.g. Route::get('/submissions', [SubmissionController::class, 'index']);
});
