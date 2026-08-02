<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BeneficiaryController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\MigrantController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReturneeController;
use App\Http\Controllers\Api\StaffController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);

    Route::apiResource('beneficiaries', BeneficiaryController::class);
    Route::get('beneficiaries/{beneficiary}/household', [BeneficiaryController::class, 'households']);
    Route::post('beneficiaries/{beneficiary}/household', [BeneficiaryController::class, 'storeHousehold']);
    Route::get('beneficiaries/{beneficiary}/interventions', [BeneficiaryController::class, 'interventions']);
    Route::post('beneficiaries/{beneficiary}/interventions', [BeneficiaryController::class, 'storeIntervention']);
    Route::get('beneficiaries/{beneficiary}/follow-ups', [BeneficiaryController::class, 'followUps']);
    Route::post('beneficiaries/{beneficiary}/follow-ups', [BeneficiaryController::class, 'storeFollowUp']);

    Route::apiResource('migrants', MigrantController::class);
    Route::get('migrants/{migrant}/documents', [MigrantController::class, 'documents']);
    Route::post('migrants/{migrant}/documents', [MigrantController::class, 'storeDocument']);
    Route::get('migrants/{migrant}/financial-records', [MigrantController::class, 'financialRecords']);
    Route::post('migrants/{migrant}/financial-records', [MigrantController::class, 'storeFinancialRecord']);
    Route::get('migrants/{migrant}/destinations', [MigrantController::class, 'destinations']);
    Route::post('migrants/{migrant}/destinations', [MigrantController::class, 'storeDestination']);

    Route::apiResource('returnees', ReturneeController::class);
    Route::get('returnees/{returnee}/plans', [ReturneeController::class, 'plans']);
    Route::post('returnees/{returnee}/plans', [ReturneeController::class, 'storePlan']);
    Route::get('returnees/{returnee}/skill-assessments', [ReturneeController::class, 'skillAssessments']);
    Route::post('returnees/{returnee}/skill-assessments', [ReturneeController::class, 'storeSkillAssessment']);
    Route::get('returnees/{returnee}/livelihood-support', [ReturneeController::class, 'livelihoodSupport']);
    Route::post('returnees/{returnee}/livelihood-support', [ReturneeController::class, 'storeLivelihoodSupport']);
    Route::get('returnees/{returnee}/follow-ups', [ReturneeController::class, 'followUps']);
    Route::post('returnees/{returnee}/follow-ups', [ReturneeController::class, 'storeFollowUp']);

    Route::apiResource('branches', BranchController::class);
    Route::apiResource('staff', StaffController::class);

    Route::get('reports/beneficiary-summary', [ReportController::class, 'beneficiarySummary']);
    Route::get('reports/migration-summary', [ReportController::class, 'migrationSummary']);
    Route::get('reports/reintegration-summary', [ReportController::class, 'reintegrationSummary']);
    Route::get('reports/branch-performance', [ReportController::class, 'branchPerformance']);
    Route::get('reports/dashboard-metrics', [ReportController::class, 'dashboardMetrics']);
    Route::get('reports/export/{type}', [ReportController::class, 'export'])->name('api.reports.export');
});
