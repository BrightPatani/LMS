<?php

use App\Http\Controllers\Api\V1\CertificateController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/certificates/{certificate}', [CertificateController::class, 'show']);
    
});