<?php

use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::put('me', [AuthController::class, 'update'])->name('auth.update');
        Route::delete('me', [AuthController::class, 'destroy'])->name('auth.destroy');
    });
});

Route::middleware('auth:sanctum')->prefix('users')->group(function (): void {
    Route::put('{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
