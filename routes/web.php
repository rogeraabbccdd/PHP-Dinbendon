<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\GroupOrderController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('stores');
    } else {
        return redirect()->route('login');
    }
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('stores', [StoreController::class, 'index'])
        ->name('stores');

    Route::get('stores/{id}', [StoreController::class, 'show'])
        ->name('stores.show');

    Route::post('group-orders', [GroupOrderController::class, 'create'])
        ->name('groupOrders.create');
});
