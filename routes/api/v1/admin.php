<?php

use App\Http\Controllers\Api\V1\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'is_admin'])->group(function () {

    Route::prefix('users')->group(function () {
        Route::get('/', [AdminController::class, 'index']);
        Route::get('/{user}', [AdminController::class, 'show']);
        Route::put('/{user}', [AdminController::class, 'update']);
        Route::patch('/{user}/role', [AdminController::class, 'changeRole']);
        Route::patch('/{user}/toggle-active', [AdminController::class, 'toggleActive']);
        Route::delete('/{user}', [AdminController::class, 'destroy']);
    });
});