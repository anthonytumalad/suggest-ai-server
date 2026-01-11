<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::middleware('auth.api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');

        Route::post('/logout_all', [AuthController::class, 'logout_all'])
            ->name('logout.all');

        Route::get('/user', [AuthController::class, 'user'])
            ->name('user');
    });

});


