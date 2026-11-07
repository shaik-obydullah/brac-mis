<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AwarenessProgramController;
use App\Http\Controllers\AwarenessProgramOutcomeController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseEnrollmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FinanceReportController;
use App\Http\Controllers\HrmController;
use App\Http\Controllers\ImmediateSupportController;
use App\Http\Controllers\JobBoardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SkillAccessController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffIncentiveController;
use App\Http\Controllers\SupportFollowUpController;
use App\Http\Controllers\SupportHistoryAnalysisController;
use App\Http\Controllers\UserController;
use App\Models\Branch;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\ImmediateSupport;
use App\Models\JournalEntry;
use App\Models\Report;
use App\Models\Staff;
use App\Models\StaffIncentive;
use App\Models\User;
use App\Models\UserMigrationHistory;
use App\Models\UserSkill;
use Illuminate\Support\Facades\Route;

Route::get('/job-board', [JobBoardController::class, 'index'])->name('job-board.index');
Route::post('/job-board/{user}/contact', [JobBoardController::class, 'submitInquiry'])
    ->middleware('throttle:10,1')
    ->whereNumber('user')
    ->name('job-board.contact');
Route::get('/job-board/{user}', [JobBoardController::class, 'show'])
    ->whereNumber('user')
    ->name('job-board.show');

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

    Route::middleware('permission:manage job board')
        ->prefix('job-board/inquiries')
        ->name('job-board.inquiries.')
        ->group(function () {
            Route::get('/', [JobBoardController::class, 'inquiries'])->name('index');
            Route::get('/{inquiry}', [JobBoardController::class, 'inquiry'])->name('show');
            Route::patch('/{inquiry}', [JobBoardController::class, 'updateInquiry'])->name('update');
        });

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
        ->group(function () {
            Route::get('/', [ImmediateSupportController::class, 'index'])
                ->middleware('permission:view immediate support')->name('index');
            Route::get('/create', [ImmediateSupportController::class, 'create'])
                ->middleware('permission:create immediate support')->name('create');
            Route::post('/', [ImmediateSupportController::class, 'store'])
                ->middleware('permission:create immediate support')->name('store');
            Route::get('/{immediateSupport}', [ImmediateSupportController::class, 'show'])
                ->middleware('permission:view immediate support')->name('show');
            Route::get('/{immediateSupport}/edit', [ImmediateSupportController::class, 'edit'])
                ->middleware('permission:edit immediate support')->name('edit');
            Route::put('/{immediateSupport}', [ImmediateSupportController::class, 'update'])
                ->middleware('permission:edit immediate support')->name('update');
            Route::delete('/{immediateSupport}', [ImmediateSupportController::class, 'destroy'])
                ->middleware('permission:delete immediate support')->name('destroy');
        });

    Route::resource('branches', BranchController::class)->only(['create', 'store'])->middleware('permission:create branches');
    Route::resource('branches', BranchController::class)->only(['index', 'show'])->middleware('permission:view branches');
    Route::resource('branches', BranchController::class)->only(['edit', 'update'])->middleware('permission:edit branches');
    Route::resource('branches', BranchController::class)->only(['destroy'])->middleware('permission:delete branches');

    Route::get('awareness-outcomes', [AwarenessProgramOutcomeController::class, 'index'])
        ->middleware('permission:view awareness programs')
        ->name('awareness-outcomes.index');
    Route::post('awareness-programs/{awarenessProgram}/outcomes', [AwarenessProgramOutcomeController::class, 'store'])
        ->middleware('permission:edit awareness programs')
        ->name('awareness-programs.outcomes.store');
    Route::patch('awareness-programs/{awarenessProgram}/outcomes/{outcome}', [AwarenessProgramOutcomeController::class, 'update'])
        ->middleware('permission:edit awareness programs')
        ->name('awareness-programs.outcomes.update');
    Route::delete('awareness-programs/{awarenessProgram}/outcomes/{outcome}', [AwarenessProgramOutcomeController::class, 'destroy'])
        ->middleware('permission:edit awareness programs')
        ->name('awareness-programs.outcomes.destroy');

    Route::resource('awareness-programs', AwarenessProgramController::class)->only(['create', 'store'])->middleware('permission:create awareness programs');
    Route::resource('awareness-programs', AwarenessProgramController::class)->only(['index', 'show'])->middleware('permission:view awareness programs');
    Route::resource('awareness-programs', AwarenessProgramController::class)->only(['edit', 'update'])->middleware('permission:edit awareness programs');
    Route::resource('awareness-programs', AwarenessProgramController::class)->only(['destroy'])->middleware('permission:delete awareness programs');

    Route::resource('courses', CourseController::class)->only(['create', 'store'])->middleware('permission:create courses');
    Route::resource('courses', CourseController::class)->only(['index', 'show'])->middleware('permission:view courses');
    Route::resource('courses', CourseController::class)->only(['edit', 'update'])->middleware('permission:edit courses');
    Route::resource('courses', CourseController::class)->only(['destroy'])->middleware('permission:delete courses');
    Route::post('courses/{course}/enrollments', [CourseEnrollmentController::class, 'store'])->middleware('permission:edit courses')->name('courses.enrollments.store');
    Route::get('courses/{course}/enrollments/{enrollment}/edit', [CourseEnrollmentController::class, 'edit'])->middleware('permission:edit courses')->name('courses.enrollments.edit');
    Route::put('courses/{course}/enrollments/{enrollment}', [CourseEnrollmentController::class, 'update'])->middleware('permission:edit courses')->name('courses.enrollments.update');
    Route::delete('courses/{course}/enrollments/{enrollment}', [CourseEnrollmentController::class, 'destroy'])->middleware('permission:edit courses')->name('courses.enrollments.destroy');

    Route::resource('certificates', CertificateController::class)->only(['create', 'store'])->middleware('permission:create certificates');
    Route::resource('certificates', CertificateController::class)->only(['index', 'show'])->middleware('permission:view certificates');
    Route::resource('certificates', CertificateController::class)->only(['edit', 'update'])->middleware('permission:edit certificates');
    Route::resource('certificates', CertificateController::class)->only(['destroy'])->middleware('permission:delete certificates');

    Route::resource('staff', StaffController::class)->only(['create', 'store'])->middleware('permission:create staff');
    Route::resource('staff', StaffController::class)->only(['index', 'show'])->middleware('permission:view staff');
    Route::resource('staff', StaffController::class)->only(['edit', 'update'])->middleware('permission:edit staff');
    Route::resource('staff', StaffController::class)->only(['destroy'])->middleware('permission:delete staff');
    Route::get('/hrm', [HrmController::class, 'index'])->middleware('permission:view staff')->name('hrm.index');

    Route::resource('designations', DesignationController::class)->only(['index'])->middleware('permission:view designations');
    Route::resource('designations', DesignationController::class)->only(['create', 'store'])->middleware('permission:create designations');
    Route::resource('designations', DesignationController::class)->only(['edit', 'update'])->middleware('permission:edit designations');
    Route::resource('designations', DesignationController::class)->only(['destroy'])->middleware('permission:delete designations');

    Route::resource('staff-incentives', StaffIncentiveController::class)->only(['create', 'store'])->middleware('permission:create staff incentives');
    Route::resource('staff-incentives', StaffIncentiveController::class)->only(['index', 'show'])->middleware('permission:view staff incentives');
    Route::resource('staff-incentives', StaffIncentiveController::class)->only(['edit', 'update'])->middleware('permission:edit staff incentives');
    Route::resource('staff-incentives', StaffIncentiveController::class)->only(['destroy'])->middleware('permission:delete staff incentives');
    Route::middleware('permission:create finances')->group(function () {
        Route::get('finances/create', [FinanceController::class, 'create'])->name('finances.create');
        Route::post('finances', [FinanceController::class, 'store'])->name('finances.store');
        Route::get('finances/accounts/create', [AccountController::class, 'create'])->name('finances.accounts.create');
        Route::post('finances/accounts', [AccountController::class, 'store'])->name('finances.accounts.store');
    });

    Route::middleware('permission:view finances')->group(function () {
        Route::get('finances', [FinanceController::class, 'index'])->name('finances.index');
        Route::get('finances/accounts', [AccountController::class, 'index'])->name('finances.accounts.index');
        Route::get('finances/trial-balance', [FinanceReportController::class, 'trialBalance'])->name('finances.trial-balance');
        Route::get('finances/income-statement', [FinanceReportController::class, 'incomeStatement'])->name('finances.income-statement');
        Route::get('finances/balance-sheet', [FinanceReportController::class, 'balanceSheet'])->name('finances.balance-sheet');
        Route::get('finances/ledger/{account}', [FinanceReportController::class, 'ledger'])->name('finances.ledger');
        Route::get('finances/{journalEntry}', [FinanceController::class, 'show'])->name('finances.show');
    });

    Route::middleware('permission:edit finances')->group(function () {
        Route::get('finances/{journalEntry}/edit', [FinanceController::class, 'edit'])->name('finances.edit');
        Route::put('finances/{journalEntry}', [FinanceController::class, 'update'])->name('finances.update');
        Route::get('finances/accounts/{account}/edit', [AccountController::class, 'edit'])->name('finances.accounts.edit');
        Route::put('finances/accounts/{account}', [AccountController::class, 'update'])->name('finances.accounts.update');
    });

    Route::middleware('permission:delete finances')->group(function () {
        Route::delete('finances/{journalEntry}', [FinanceController::class, 'destroy'])->name('finances.destroy');
        Route::delete('finances/accounts/{account}', [AccountController::class, 'destroy'])->name('finances.accounts.destroy');
    });

    Route::middleware('permission:view reports')->group(function () {
        Route::get('/reports', function () {
            return view('reports.index', [
                'totalProfiles' => User::count(),
                'activeProfiles' => User::where('status', 'active')->count(),
                'reintegratedProfiles' => User::reintegrated()->count(),
                'potentialCustomers' => User::potentialCustomers()->count(),
                'totalSkills' => UserSkill::count(),
                'totalMigrationHistories' => UserMigrationHistory::count(),
                'totalBranches' => Branch::count(),
                'totalStaff' => Staff::count(),
                'totalCourses' => Course::count(),
                'activeCourses' => Course::where('status', 'active')->count(),
                'totalCertificates' => Certificate::count(),
                'validCertificates' => Certificate::where('status', 'valid')->count(),
                'totalSupportRecords' => ImmediateSupport::count(),
                'totalStaffIncentives' => StaffIncentive::count(),
                'paidIncentiveAmount' => (float) StaffIncentive::where('status', StaffIncentive::STATUS_PAID)->sum('amount'),
                'postedJournalEntries' => JournalEntry::posted()->count(),
                'recentExports' => Report::with('generatedBy')->latest()->take(6)->get(),
            ]);
        })->name('reports.index');

        Route::get('/reports/export/{type}/{format}', [ExportController::class, 'export'])
            ->middleware('permission:export reports')
            ->name('reports.export');
    });

    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index')
        ->middleware('permission:view audit logs');

    Route::middleware('permission:manage settings')->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });

    Route::middleware('permission:manage users')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/skill-access', [SkillAccessController::class, 'index'])->name('skill-access.index');
        Route::post('/skill-access/{user}/skills', [SkillAccessController::class, 'store'])->name('skill-access.skills.store');
        Route::delete('/skill-access/{user}/skills/{skill}', [SkillAccessController::class, 'destroy'])->name('skill-access.skills.destroy');
        Route::post('/skill-access/{user}/profile-type', [SkillAccessController::class, 'updateProfileType'])->name('skill-access.profile-type');
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
