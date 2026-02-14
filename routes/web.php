<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\TableController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ColumnController;
use App\Http\Controllers\Admin\RowController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\SiteSettingsController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [TableController::class, 'index'])->name('home');
Route::get('/package/{slug}', [TableController::class, 'showPackage'])->name('package.show');
Route::get('/plan/{slug}', [TableController::class, 'showPlan'])->name('plan.show');
Route::post('/plan/{slug}/filter', [TableController::class, 'filter'])->name('plan.filter');
Route::get('/plan/{slug}/export', [TableController::class, 'export'])->name('plan.export');
Route::get('/package/{slug}/export', [TableController::class, 'exportPackage'])->name('package.export');

// Legacy routes for backward compatibility
Route::get('/table/{slug}', [TableController::class, 'showPlan'])->name('table.show');
Route::post('/table/{slug}/filter', [TableController::class, 'filter'])->name('table.filter');
Route::get('/table/{slug}/export', [TableController::class, 'export'])->name('table.export');

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Protected Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth', 'role:admin|editor'])->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Packages Management
    Route::resource('packages', PackageController::class);
    Route::post('packages/{package}/toggle', [PackageController::class, 'toggle'])->name('packages.toggle');
    Route::post('packages/reorder', [PackageController::class, 'reorder'])->name('packages.reorder');

    // Plans Management (under packages)
    Route::get('packages/{package}/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('packages/{package}/plans/create', [PlanController::class, 'create'])->name('plans.create');
    Route::post('packages/{package}/plans', [PlanController::class, 'store'])->name('plans.store');
    Route::get('plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
    Route::put('plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
    Route::delete('plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');
    Route::post('plans/{plan}/toggle', [PlanController::class, 'toggle'])->name('plans.toggle');
    Route::post('plans/reorder', [PlanController::class, 'reorder'])->name('plans.reorder');

    // Columns Management (under plans)
    Route::get('plans/{plan}/columns', [ColumnController::class, 'index'])->name('columns.index');
    Route::post('plans/{plan}/columns', [ColumnController::class, 'store'])->name('columns.store');
    Route::put('columns/{column}', [ColumnController::class, 'update'])->name('columns.update');
    Route::delete('columns/{column}', [ColumnController::class, 'destroy'])->name('columns.destroy');
    Route::post('columns/reorder', [ColumnController::class, 'reorder'])->name('columns.reorder');

    // Columns Management (under packages - for media type)
    Route::get('packages/{package}/columns', [ColumnController::class, 'indexForPackage'])->name('columns.index.package');
    Route::post('packages/{package}/columns', [ColumnController::class, 'storeForPackage'])->name('columns.store.package');

    // Rows Management (under plans)
    Route::get('plans/{plan}/rows', [RowController::class, 'index'])->name('rows.index');
    Route::post('plans/{plan}/rows', [RowController::class, 'store'])->name('rows.store');
    Route::put('rows/{row}', [RowController::class, 'update'])->name('rows.update');
    Route::delete('rows/{row}', [RowController::class, 'destroy'])->name('rows.destroy');
    Route::post('plans/{plan}/rows/import', [RowController::class, 'import'])->name('rows.import');
    Route::post('rows/reorder', [RowController::class, 'reorder'])->name('rows.reorder');

    // Rows Management (under packages - for media type)
    Route::get('packages/{package}/rows', [RowController::class, 'indexForPackage'])->name('rows.index.package');
    Route::post('packages/{package}/rows', [RowController::class, 'storeForPackage'])->name('rows.store.package');
    Route::post('packages/{package}/rows/import', [RowController::class, 'importForPackage'])->name('rows.import.package');

    // Languages Management
    Route::resource('languages', LanguageController::class);
    Route::post('languages/{language}/default', [LanguageController::class, 'setDefault'])->name('languages.default');
    Route::post('languages/{language}/toggle', [LanguageController::class, 'toggle'])->name('languages.toggle');

    // Site Settings
    Route::get('settings', [SiteSettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SiteSettingsController::class, 'update'])->name('settings.update');
    Route::delete('settings/logo', [SiteSettingsController::class, 'removeLogo'])->name('settings.removeLogo');
    Route::delete('settings/favicon', [SiteSettingsController::class, 'removeFavicon'])->name('settings.removeFavicon');
});
