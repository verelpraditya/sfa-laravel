<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth'])->name('dashboard');

Route::prefix('workspace')->middleware(['auth'])->group(function () {
    Route::view('/admin', 'dashboard')->middleware('role:admin_pusat')->name('workspace.admin');
    Route::view('/supervisor', 'dashboard')->middleware('role:supervisor')->name('workspace.supervisor');
    Route::view('/sales', 'dashboard')->middleware('role:sales')->name('workspace.sales');
    Route::view('/smd', 'dashboard')->middleware('role:smd')->name('workspace.smd');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/outlets/search', [OutletController::class, 'search'])->name('ajax.outlets.search');
    Route::resource('outlets', OutletController::class)->except(['show', 'destroy']);
});

require __DIR__.'/auth.php';
