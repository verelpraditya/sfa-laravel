<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\OutletMergeController;
use App\Http\Controllers\OutletVerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesVisitController;
use App\Http\Controllers\SmdVisitController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\VisitHistoryController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

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
    Route::get('/visit-history', [VisitHistoryController::class, 'index'])->name('visit-history.index');
    Route::get('/visit-history/{visit}', [VisitHistoryController::class, 'show'])->name('visit-history.show');

    Route::middleware('role:admin_pusat,supervisor')->group(function () {
        Route::get('/visit-history/{visit}/edit', [VisitHistoryController::class, 'edit'])->name('visit-history.edit');
        Route::put('/visit-history/{visit}', [VisitHistoryController::class, 'update'])->name('visit-history.update');
        Route::delete('/visit-history/{visit}', [VisitHistoryController::class, 'destroy'])->name('visit-history.destroy');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

    Route::middleware('role:admin_pusat')->group(function () {
        Route::resource('branches', BranchController::class)->except(['show', 'destroy']);
        Route::resource('users', UserManagementController::class)->except(['show', 'destroy']);
        Route::delete('/outlets/{outlet}', [OutletController::class, 'destroy'])->name('outlets.destroy');
    });

    // Duplicate detection & merge — MUST be registered before Route::resource('outlets')
    Route::middleware('role:admin_pusat,supervisor')->group(function () {
        Route::get('/outlets/duplicates', [OutletMergeController::class, 'index'])->name('outlets.duplicates');
        Route::get('/outlets/duplicates/{outlet}', [OutletMergeController::class, 'show'])->name('outlets.merge');
        Route::post('/outlets/duplicates/{outlet}/merge', [OutletMergeController::class, 'merge'])->name('outlets.merge.execute');
    });

    Route::get('/outlets/search', [OutletController::class, 'search'])->name('ajax.outlets.search');
    Route::resource('outlets', OutletController::class)->except(['destroy']);
    Route::get('/outlet-lists/prospects', [OutletController::class, 'prospects'])->name('outlet-lists.prospects');
    Route::get('/outlet-lists/noo', [OutletController::class, 'noo'])->name('outlet-lists.noo');
    Route::get('/outlet-lists/inactive', [OutletController::class, 'inactive'])->name('outlet-lists.inactive');

    Route::middleware('role:admin_pusat,supervisor')->group(function () {
        Route::get('/outlet-verifications', [OutletVerificationController::class, 'index'])->name('outlet-verifications.index');
        Route::get('/outlet-verifications/{outlet}/edit', [OutletVerificationController::class, 'edit'])->name('outlet-verifications.edit');
        Route::put('/outlet-verifications/{outlet}', [OutletVerificationController::class, 'update'])->name('outlet-verifications.update');
    });

    Route::middleware('role:sales,supervisor')->group(function () {
        Route::get('/sales-visits', [SalesVisitController::class, 'index'])->name('sales-visits.index');
        Route::get('/sales-visits/create', [SalesVisitController::class, 'create'])->name('sales-visits.create');
        Route::post('/sales-visits', [SalesVisitController::class, 'store'])->name('sales-visits.store');
    });

    Route::middleware('role:smd,supervisor')->group(function () {
        Route::get('/smd-visits', [SmdVisitController::class, 'index'])->name('smd-visits.index');
        Route::get('/smd-visits/create', [SmdVisitController::class, 'create'])->name('smd-visits.create');
        Route::post('/smd-visits', [SmdVisitController::class, 'store'])->name('smd-visits.store');
    });
});

require __DIR__.'/auth.php';
