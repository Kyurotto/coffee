<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard – branching view handled inside DashboardController
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin,employee'])
    ->name('dashboard');

// Order routes – protected, both admin and employee can access
Route::middleware(['auth', 'role:admin,employee'])->group(function () {
    // Employee: submit a new POS order (AJAX)
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    // Admin / any device: fetch live order summary as JSON
    Route::get('/orders/summary', [OrderController::class, 'summary'])
        ->middleware('role:admin')
        ->name('orders.summary');

    // Live product feed for the employee POS
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
});

// Admin-only area
Route::get('/admin', function () {
    return "Welcome Admin! This is a restricted area.";
})->middleware(['auth', 'role:admin'])->name('admin.index');

// Product CRUD (admin only)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::post('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/ajax-update', [ProfileController::class, 'ajaxUpdate'])->name('profile.ajax-update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
