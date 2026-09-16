<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome'); })->name('home');


Route::prefix('main')->group(function () {
Route::get('/', [ItemController::class, 'index'])->name('main.index');


Route::get('/create', [ItemController::class, 'create'])->name('main.create');

Route::post('/', [ItemController::class, 'store'])->name('main.store');

Route::get('/{item}', [ItemController::class, 'show'])->name('main.show');

Route::get('/{item}/edit', [ItemController::class, 'edit'])->name('main.edit');

Route::put('/{item}', [ItemController::class, 'update'])->name('main.update');

Route::delete('/{item}', [ItemController::class, 'destroy'])->name('main.destroy');



});