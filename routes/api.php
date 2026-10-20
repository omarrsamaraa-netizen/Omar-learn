<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/luxury', function () {
    return response()->json([
        'message' => 'luxury here',

    ]);
});

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('api.login');

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'show'])->name('show');

        Route::delete('/', [CartController::class, 'clear'])->name('clear');

        Route::post('/{item}', [CartController::class, 'store'])->name('store');

        Route::put('/{item}', [CartController::class, 'update'])->name('update');

        Route::delete('/{item}', [CartController::class, 'destroy'])->name('destroy');
    });

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::get('/user', [UserController::class, 'show'])->name('user.show');
});
