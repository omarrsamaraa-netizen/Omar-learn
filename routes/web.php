<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('main')->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('main.index');

    Route::get('/trash', [ItemController::class, 'trash'])->name('main.trash');

    Route::delete('/trash', [ItemController::class, 'emptyTrash'])->name('main.trash.empty');

    Route::get('/create', [ItemController::class, 'create'])->name('main.create');

    Route::post('/', [ItemController::class, 'store'])->name('main.store');

    Route::get('/{item}', [ItemController::class, 'show'])->withTrashed()->name('main.show');

    Route::get('/{item}/edit', [ItemController::class, 'edit'])->name('main.edit');

    Route::put('/{item}', [ItemController::class, 'update'])->name('main.update');

    Route::delete('/{item}', [ItemController::class, 'destroy'])->name('main.destroy');

    Route::delete('/{item}/force', [ItemController::class, 'permenentDelete'])->withTrashed()->name('main.force-delete');

    Route::resource('products', ItemController::class);

    Route::get('/products', [ItemController::class, 'index']);
    Route::get('/products/{product}', [ItemController::class, 'show']);

});

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');

    Route::delete('/', [CartController::class, 'clear'])->name('cart.clear');

    Route::post('/{item}', [CartController::class, 'store'])->name('cart.store');

    Route::put('/{item}', [CartController::class, 'update'])->name('cart.update');

    Route::delete('/{item}', [CartController::class, 'destroy'])->name('cart.destroy');
});

Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

Route::middleware(['auth', AdminMiddleware::class])->group(function () {

    Route::get('/admin/products', [ItemController::class, 'index']);

    Route::get('/admin/products/create', [ItemController::class, 'create']);

    Route::post('/admin/products', [ItemController::class, 'store']);

    Route::get('/admin/products/{product}/edit', [ItemController::class, 'edit']);

    Route::put('/admin/products/{product}', [ItemController::class, 'update']);

    Route::delete('/admin/products/{product}', [ItemController::class, 'destroy']);

});
