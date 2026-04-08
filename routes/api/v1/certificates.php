<?php

use App\Http\Controllers\Api\V1\CertificateController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/certificates/{certificate}', [CertificateController::class, 'show']);
    
});