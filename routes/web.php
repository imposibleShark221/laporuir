<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Panel admin masih tampilan dengan data contoh dan belum dibatasi peran.
// ganti dengan middleware peran admin saat login fase 3 dibuat.
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/laporan', [AdminController::class, 'reports'])->name('reports');
    Route::get('/lokasi', [AdminController::class, 'locations'])->name('locations');
    Route::get('/aktivitas', [AdminController::class, 'activity'])->name('activity');
});
