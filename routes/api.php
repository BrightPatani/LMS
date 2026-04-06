<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
require __DIR__.'/api/v1/auth.php';

Route::prefix('v1')->group(function () {
    require __DIR__.'/api/v1/courses.php';
    require __DIR__.'/api/v1/lessons.php';
    require __DIR__.'/api/v1/enrollments.php';
    require __DIR__.'/api/v1/assignments.php';
    require __DIR__.'/api/v1/submissions.php';
    require __DIR__.'/api/v1/comments.php';
    require __DIR__.'/api/v1/certificates.php';
    require __DIR__.'/api/v1/files.php';
});
