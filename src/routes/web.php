<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\JobBoardController;
use App\Http\Controllers\MigrantController;
use App\Http\Controllers\ReturneeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/job-board', [JobBoardController::class, 'index'])->name('job-board.index');
Route::get('/job-board/{type}/{id}', [JobBoardController::class, 'show'])->where('type', 'beneficiary|returnee')->whereNumber('id')->name('job-board.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index']);

    Route::resource('beneficiaries', BeneficiaryController::class)->middleware('permission:view beneficiaries|create beneficiaries|edit beneficiaries|delete beneficiaries');
    Route::resource('migrants', MigrantController::class)->middleware('permission:view migrants|create migrants|edit migrants|delete migrants');
    Route::resource('returnees', ReturneeController::class)->middleware('permission:view returnees|create returnees|edit returnees|delete returnees');
    Route::resource('branches', BranchController::class)->middleware('permission:view branches|create branches|edit branches|delete branches');
    Route::resource('staff', StaffController::class)->middleware('permission:view staff|create staff|edit staff|delete staff');

    Route::get('/beneficiaries/{beneficiary}/documents', [DocumentController::class, 'beneficiaryIndex'])->name('beneficiaries.documents.index');
    Route::post('/beneficiaries/{beneficiary}/documents', [DocumentController::class, 'beneficiaryUpload'])->name('beneficiaries.documents.upload');
    Route::delete('/beneficiaries/{beneficiary}/documents/{document}', [DocumentController::class, 'beneficiaryDestroy'])->name('beneficiaries.documents.destroy');
    Route::get('/migrants/{migrant}/documents', [DocumentController::class, 'migrantIndex'])->name('migrants.documents.index');
    Route::post('/migrants/{migrant}/documents', [DocumentController::class, 'migrantUpload'])->name('migrants.documents.upload');
    Route::delete('/migrants/{migrant}/documents/{document}', [DocumentController::class, 'migrantDestroy'])->name('migrants.documents.destroy');

    Route::middleware('permission:view reports|view audit logs')->group(function () {
        Route::get('/reports', function () {
            return view('reports.index', [
                'totalBeneficiaries' => \App\Models\Beneficiary::count(),
                'activeBeneficiaries' => \App\Models\Beneficiary::where('status', 'active')->count(),
                'migratedBeneficiaries' => \App\Models\Migrant::distinct('beneficiary_id')->count('beneficiary_id'),
                'totalMigrants' => \App\Models\Migrant::count(),
                'activeMigrants' => \App\Models\Migrant::where('status', 'deployed')->count(),
                'returnedMigrants' => \App\Models\Migrant::where('status', 'returned')->count(),
                'totalReturnees' => \App\Models\Returnee::count(),
                'reintegratedReturnees' => \App\Models\Returnee::where('current_status', 'completed')->count(),
                'inProgressReturnees' => \App\Models\Returnee::whereIn('current_status', ['assessed', 'planning', 'in_progress'])->count(),
                'totalBranches' => \App\Models\Branch::count(),
                'totalStaff' => \App\Models\Staff::count(),
            ]);
        })->name('reports.index');

        Route::get('/reports/export/{type}/{format}', [ExportController::class, 'export'])->name('reports.export');

        Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index')->middleware('permission:view audit logs');
    });

    Route::middleware('permission:manage roles|manage permissions')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/users', [RoleController::class, 'users'])->name('roles.users');
        Route::post('/roles/users/{user}/assign', [RoleController::class, 'assignRole'])->name('roles.users.assign');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});
