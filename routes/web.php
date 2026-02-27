<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalibrationController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\GageController;
use App\Http\Controllers\MetadataController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth.gagetrack')->group(function () {

    // Home → calendar
    Route::get('/', fn() => redirect()->route('calendar.index'));

    // Calendar
    Route::get('/calendar/{year?}', [CalendarController::class, 'index'])->name('calendar.index');

    // Gages
    Route::get('/gages', [GageController::class, 'index'])->name('gages.index');
    Route::post('/gages/search', [GageController::class, 'index'])->name('gages.search');
    Route::get('/gages/create', [GageController::class, 'create'])->name('gages.create');
    Route::post('/gages', [GageController::class, 'store'])->name('gages.store');
    Route::get('/gages/{gage}/edit', [GageController::class, 'edit'])->name('gages.edit');
    Route::put('/gages/{gage}', [GageController::class, 'update'])->name('gages.update');
    Route::delete('/gages/{gage}', [GageController::class, 'destroy'])->name('gages.destroy');

    // Calibrations
    Route::get('/calibrations', [CalibrationController::class, 'index'])->name('calibrations.index');
    Route::post('/calibrations/search', [CalibrationController::class, 'index'])->name('calibrations.search');
    Route::get('/calibrations/create', [CalibrationController::class, 'create'])->name('calibrations.create');
    Route::post('/calibrations/store', [CalibrationController::class, 'store'])->name('calibrations.store');
    Route::get('/calibrations/{calibration}/edit', [CalibrationController::class, 'edit'])->name('calibrations.edit');
    Route::put('/calibrations/{calibration}', [CalibrationController::class, 'update'])->name('calibrations.update');

    // Suppliers
    Route::resource('suppliers', SupplierController::class)->except(['show']);

    // Metadata
    Route::get('/metadata', [MetadataController::class, 'index'])->name('metadata.index');
    Route::post('/metadata/search', [MetadataController::class, 'index'])->name('metadata.search');
    Route::get('/metadata/create', [MetadataController::class, 'create'])->name('metadata.create');
    Route::post('/metadata/store', [MetadataController::class, 'store'])->name('metadata.store');
    Route::get('/metadata/{metadata}/edit', [MetadataController::class, 'edit'])->name('metadata.edit');
    Route::put('/metadata/{metadata}', [MetadataController::class, 'update'])->name('metadata.update');
    Route::delete('/metadata/{metadata}', [MetadataController::class, 'destroy'])->name('metadata.destroy');

    // Configurations
    Route::resource('configurations', ConfigurationController::class)->except(['show', 'create', 'store', 'destroy']);

    // Procedures
    Route::resource('procedures', ProcedureController::class)->except(['show']);

    // Users
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password.post');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/gages', [ReportController::class, 'gages'])->name('reports.gages');
    Route::post('/reports/gages', [ReportController::class, 'gages'])->name('reports.gages.search');
    Route::get('/reports/backlog', [ReportController::class, 'backlog'])->name('reports.backlog');
    Route::get('/reports/certifications/{gageId?}', [ReportController::class, 'certifications'])->name('reports.certifications');
    Route::post('/reports/certifications', [ReportController::class, 'certifications'])->name('reports.certifications.search');

    // Certificate file download
    Route::get('/files/{filename}', [CalibrationController::class, 'downloadCertificate'])->name('certificate.download');
});
