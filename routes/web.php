<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\GroupOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CommentController;
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

    Route::get('stores/new', [StoreController::class, 'createForm'])
        ->name('stores.new');

    Route::get('stores/{id}', [StoreController::class, 'show'])
        ->name('stores.show');

    Route::get('stores/{id}/edit', [StoreController::class, 'editForm'])
        ->name('stores.edit');

    Route::post('stores', [StoreController::class, 'editFormSubmit'])
        ->name('stores.edit.submit');

    Route::get('group-orders', [GroupOrderController::class, 'index'])
        ->name('groupOrders');

    Route::post('group-orders', [GroupOrderController::class, 'create'])
        ->name('groupOrders.create');

    Route::get('group-orders/{id}', [GroupOrderController::class, 'show'])
        ->name('groupOrders.show');

    Route::post('group-orders/{id}', [GroupOrderController::class, 'update'])
        ->name('groupOrders.update');

    Route::post('group-orders/{id}/on_time', [GroupOrderController::class, 'updateOnTime'])
        ->name('groupOrders.updateOnTime');

    Route::get('group-orders/{id}/order', [OrderController::class, 'showForm'])
        ->name('groupOrders.order');

    Route::post('group-orders/{id}/order', [OrderController::class, 'create'])
        ->name('groupOrders.order.create');

    Route::get('orders', [OrderController::class, 'index'])
        ->name('orders');

    Route::delete('orders/{id}', [OrderController::class, 'cancel'])
        ->name('orders.cancel');

    Route::post('comments', [CommentController::class, 'submit'])
        ->name('comments.submit');
});
