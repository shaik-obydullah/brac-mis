<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AwarenessProgramController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseEnrollmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\HrmController;
use App\Http\Controllers\ImmediateSupportController;
use App\Http\Controllers\JobBoardController;
use App\Http\Controllers\MigrantController;
use App\Http\Controllers\ReturneeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SkillAccessController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffIncentiveController;
use App\Http\Controllers\SupportFollowUpController;
use App\Http\Controllers\SupportHistoryAnalysisController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/job-board', [JobBoardController::class, 'index'])->name('job-board.index');
Route::get('/job-board/{type}/{id}', [JobBoardController::class, 'show'])->where('type', 'beneficiary|returnee')->whereNumber('id')->name('job-board.show');

Route::get('/certificates/validate', [CertificateController::class, 'validateForm'])->name('certificates.validate');
Route::post('/certificates/validate', [CertificateController::class, 'validateCertificate'])->name('certificates.validate.submit');
Route::get('/certificates/verify/{code}', [CertificateController::class, 'verify'])->name('certificates.verify');

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
    Route::get('immediate-supports', fn () => redirect()->route('support.index', ['category' => 'immediate']));

    Route::get('support/history-analysis', [SupportHistoryAnalysisController::class, 'index'])
        ->middleware('permission:view immediate support')
        ->name('support.history');

    Route::get('support-follow-ups', [SupportFollowUpController::class, 'index'])
        ->middleware('permission:view immediate support')
        ->name('support-follow-ups.index');
    Route::get('support-follow-ups/create', [SupportFollowUpController::class, 'create'])
        ->middleware('permission:create immediate support')
        ->name('support-follow-ups.create');
    Route::post('support-follow-ups', [SupportFollowUpController::class, 'store'])
        ->middleware('permission:create immediate support')
        ->name('support-follow-ups.store');
    Route::get('support-follow-ups/{supportFollowUp}/edit', [SupportFollowUpController::class, 'edit'])
        ->middleware('permission:edit immediate support')
        ->name('support-follow-ups.edit');
    Route::put('support-follow-ups/{supportFollowUp}', [SupportFollowUpController::class, 'update'])
        ->middleware('permission:edit immediate support')
        ->name('support-follow-ups.update');
    Route::delete('support-follow-ups/{supportFollowUp}', [SupportFollowUpController::class, 'destroy'])
        ->middleware('permission:delete immediate support')
        ->name('support-follow-ups.destroy');

    Route::prefix('support/{category}')
        ->where(['category' => 'immediate|mental-psychological|physical|financial'])
        ->name('support.')
        ->middleware('permission:view immediate support|create immediate support|edit immediate support|delete immediate support')
        ->group(function () {
            Route::get('/', [ImmediateSupportController::class, 'index'])->name('index');
            Route::get('/create', [ImmediateSupportController::class, 'create'])->name('create');
            Route::post('/', [ImmediateSupportController::class, 'store'])->name('store');
            Route::get('/{immediateSupport}', [ImmediateSupportController::class, 'show'])->name('show');
            Route::get('/{immediateSupport}/edit', [ImmediateSupportController::class, 'edit'])->name('edit');
            Route::put('/{immediateSupport}', [ImmediateSupportController::class, 'update'])->name('update');
            Route::delete('/{immediateSupport}', [ImmediateSupportController::class, 'destroy'])->name('destroy');
        });
    Route::resource('branches', BranchController::class)->middleware('permission:view branches|create branches|edit branches|delete branches');
    Route::resource('awareness-programs', AwarenessProgramController::class)->middleware('permission:view awareness programs|create awareness programs|edit awareness programs|delete awareness programs');

    Route::resource('courses', CourseController::class)->middleware('permission:view courses|create courses|edit courses|delete courses');
    Route::post('courses/{course}/enrollments', [CourseEnrollmentController::class, 'store'])->middleware('permission:edit courses')->name('courses.enrollments.store');
    Route::get('courses/{course}/enrollments/{enrollment}/edit', [CourseEnrollmentController::class, 'edit'])->middleware('permission:edit courses')->name('courses.enrollments.edit');
    Route::put('courses/{course}/enrollments/{enrollment}', [CourseEnrollmentController::class, 'update'])->middleware('permission:edit courses')->name('courses.enrollments.update');
    Route::delete('courses/{course}/enrollments/{enrollment}', [CourseEnrollmentController::class, 'destroy'])->middleware('permission:edit courses')->name('courses.enrollments.destroy');

    Route::resource('certificates', CertificateController::class)->middleware('permission:view certificates|create certificates|edit certificates|delete certificates');

    Route::resource('staff', StaffController::class)->middleware('permission:view staff|create staff|edit staff|delete staff');
    Route::get('/hrm', [HrmController::class, 'index'])->middleware('permission:view staff')->name('hrm.index');
    Route::resource('designations', DesignationController::class)->except(['show'])->middleware('permission:view designations|create designations|edit designations|delete designations');
    Route::resource('staff-incentives', StaffIncentiveController::class)->middleware('permission:view staff incentives|create staff incentives|edit staff incentives|delete staff incentives');
    Route::resource('finances', FinanceController::class)->middleware('permission:view finances|create finances|edit finances|delete finances');

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

    Route::middleware('permission:manage settings')->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });

    Route::middleware('permission:manage users')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/skill-access', [SkillAccessController::class, 'index'])->name('skill-access.index');
        Route::post('/skill-access/{user}/skills', [SkillAccessController::class, 'store'])->name('skill-access.skills.store');
        Route::delete('/skill-access/{user}/skills/{skill}', [SkillAccessController::class, 'destroy'])->name('skill-access.skills.destroy');
        Route::post('/users/{user}/skills', [UserController::class, 'storeSkill'])->name('users.skills.store');
        Route::delete('/users/{user}/skills/{skill}', [UserController::class, 'destroySkill'])->name('users.skills.destroy');
        Route::post('/users/{user}/migration-histories', [UserController::class, 'storeMigrationHistory'])->name('users.migration-histories.store');
        Route::delete('/users/{user}/migration-histories/{history}', [UserController::class, 'destroyMigrationHistory'])->name('users.migration-histories.destroy');
        Route::post('/users/{user}/documents', [UserController::class, 'storeDocument'])->name('users.documents.store');
        Route::delete('/users/{user}/documents/{document}', [UserController::class, 'destroyDocument'])->name('users.documents.destroy');
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
