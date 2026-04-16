<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ClientController;
use App\Http\Controllers\Web\UserManagementController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', function (): RedirectResponse {
    return redirect()->route('dashboard.page');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:login')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.page');

    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/create', [ClientController::class, 'create'])->name('create');
        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
        Route::put('/{client}', [ClientController::class, 'update'])->name('update');
        Route::get('/{client}', [ClientController::class, 'show'])->name('show');
        Route::post('/', [ClientController::class, 'store'])->name('store');
    });

        // Quotation routes
        Route::get('/quotations', [\App\Http\Controllers\Web\QuotationController::class, 'index'])->name('quotations.index');
        Route::get('/quotations/create', [\App\Http\Controllers\Web\QuotationController::class, 'create'])->name('quotations.create');
        Route::post('/quotations', [\App\Http\Controllers\Web\QuotationController::class, 'store'])->name('quotations.store');
        Route::get('/quotations/{quotation}/edit', [\App\Http\Controllers\Web\QuotationController::class, 'edit'])->name('quotations.edit');
        Route::put('/quotations/{quotation}', [\App\Http\Controllers\Web\QuotationController::class, 'update'])->name('quotations.update');
        Route::get('/quotations/{quotation}/history', [\App\Http\Controllers\Web\QuotationController::class, 'history'])->name('quotations.history');
        Route::delete('/quotations/{quotation}', [\App\Http\Controllers\Web\QuotationController::class, 'destroy'])->name('quotations.destroy');

        // Reports
        Route::get('/reports', [\App\Http\Controllers\Web\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [\App\Http\Controllers\Web\ReportController::class, 'exportCsv'])->name('reports.export');
        Route::get('/reports/export-sales-monthly', [\App\Http\Controllers\Web\ReportController::class, 'exportSalesMonthlyCsv'])->name('reports.export.sales-monthly');

    Route::middleware('can:manage-users')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
    });
});
