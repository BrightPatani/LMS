<?php

use App\Http\Controllers\Api\V1\Instructor\InstructorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'isInstructor'])->group(function () {
    Route::get('/dashboard', [InstructorController::class, 'dashboard']);
    
    Route::prefix('courses/{course}')->group(function () {
        Route::get('/students', [InstructorController::class, 'courseStudents']);
        Route::get('/submissions', [InstructorController::class, 'courseSubmissions']);
        Route::get('/analytics', [InstructorController::class, 'courseAnalytics']);
        Route::patch('/publish', [InstructorController::class, 'togglePublish']);
        Route::get('/assignments', [InstructorController::class, 'courseAssignments']);
        Route::get('/assignments/{assignment}/submissions', [InstructorController::class, 'assignmentSubmissions']);
    });

    Route::put('/submissions/{submission}/grade', [InstructorController::class, 'gradeSubmission']);
});