<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\StaffController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'active'])->name('api.')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);

    // Branch endpoints mirror the web permissions: reading needs "view",
    // while each write action needs its own create/edit/delete permission.
    Route::middleware('permission:view branches')->group(function () {
        Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
        Route::get('branches/{branch}', [BranchController::class, 'show'])->name('branches.show');
    });

    Route::middleware('permission:create branches')->group(function () {
        Route::post('branches', [BranchController::class, 'store'])->name('branches.store');
    });

    Route::middleware('permission:edit branches')->group(function () {
        Route::put('branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
        Route::patch('branches/{branch}', [BranchController::class, 'update']);
    });

    Route::middleware('permission:delete branches')->group(function () {
        Route::delete('branches/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');
    });

    Route::middleware('permission:view staff')->group(function () {
        Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('staff/{staff}', [StaffController::class, 'show'])->name('staff.show');
    });

    Route::middleware('permission:create staff')->group(function () {
        Route::post('staff', [StaffController::class, 'store'])->name('staff.store');
    });

    Route::middleware('permission:edit staff')->group(function () {
        Route::put('staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
        Route::patch('staff/{staff}', [StaffController::class, 'update']);
    });

    Route::middleware('permission:delete staff')->group(function () {
        Route::delete('staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
    });

    Route::middleware('permission:view reports')->group(function () {
        Route::get('reports/profile-summary', [ReportController::class, 'summary']);
        Route::get('reports/profiles-by-branch', [ReportController::class, 'profilesByBranch']);
        Route::get('reports/profiles-by-type', [ReportController::class, 'profilesByType']);
        Route::get('reports/migration-summary', [ReportController::class, 'migrationSummary']);
        Route::get('reports/branch-performance', [ReportController::class, 'branchPerformance']);

        Route::get('reports/export/{type}', [ReportController::class, 'export'])
            ->middleware('permission:export reports')
            ->name('api.reports.export');
    });
});
