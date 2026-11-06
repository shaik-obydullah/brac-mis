<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\StaffController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);

    Route::apiResource('branches', BranchController::class);
    Route::apiResource('staff', StaffController::class);

    Route::get('reports/profile-summary', [ReportController::class, 'summary']);
    Route::get('reports/profiles-by-branch', [ReportController::class, 'profilesByBranch']);
    Route::get('reports/profiles-by-type', [ReportController::class, 'profilesByType']);
    Route::get('reports/migration-summary', [ReportController::class, 'migrationSummary']);
    Route::get('reports/branch-performance', [ReportController::class, 'branchPerformance']);
    Route::get('reports/export/{type}', [ReportController::class, 'export'])->name('api.reports.export');
});
