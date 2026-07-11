<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
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
});

// Admin-only area
Route::get('/admin', function () {
    return "Welcome Admin! This is a restricted area.";
})->middleware(['auth', 'role:admin'])->name('admin.index');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
