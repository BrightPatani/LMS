<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::prefix('auth')->group(base_path('routes/api/v1/auth.php'));
    Route::prefix('courses')->group(base_path('routes/api/v1/courses.php'));
    Route::prefix('enrollments')->group(base_path('routes/api/v1/enrollments.php'));
    Route::prefix('assignments')->group(base_path('routes/api/v1/assignments.php'));
    Route::prefix('comments')->group(base_path('routes/api/v1/comments.php'));
    Route::prefix('certificates')->group(base_path('routes/api/v1/certificates.php'));
    Route::prefix('files')->group(base_path('routes/api/v1/files.php'));

    Route::middleware(['auth:api', 'isStudent'])->group(function () {
        Route::get('/courses/enrolled', [\App\Http\Controllers\Api\V1\EnrollmentController::class, 'myCourses']);
    }); // This route allows authenticated students to retrieve a list of courses they are enrolled in by calling the 'myCourses' method of the EnrollmentController.
});
