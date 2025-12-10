<?php

use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Locations\Controllers\CityController;
use App\Modules\Menu\Controllers\IngredientController;
use App\Modules\Menu\Controllers\PizzaController;
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

Route::prefix('ingredients')->group(function (): void {
    Route::get('/', [IngredientController::class, 'index'])->name('ingredients.index');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/', [IngredientController::class, 'store'])->name('ingredients.store');
        Route::put('{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update');
    });
});

Route::prefix('pizzas')->group(function (): void {
    Route::get('/', [PizzaController::class, 'index'])->name('pizzas.index');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/', [PizzaController::class, 'store'])->name('pizzas.store');
        Route::put('{pizza}', [PizzaController::class, 'update'])->name('pizzas.update');
    });
});

Route::prefix('cities')->group(function (): void {
    Route::get('/', [CityController::class, 'index'])->name('cities.index');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/', [CityController::class, 'store'])->name('cities.store');
        Route::put('{city}', [CityController::class, 'update'])->name('cities.update');
        Route::delete('{city}', [CityController::class, 'destroy'])->name('cities.destroy');
    });
});
