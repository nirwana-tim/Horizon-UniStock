<?php

use App\Http\Controllers\Finance\StockOpnameController;
use App\Http\Controllers\Finance\GpmController;
use App\Http\Controllers\Finance\EligibilityController;
use App\Http\Controllers\Master\DistributionScheduleController;
use App\Http\Controllers\Master\EntitlementController;
use App\Http\Controllers\Master\FacultyController;
use App\Http\Controllers\Master\ItemCategoryController;
use App\Http\Controllers\Master\ItemController;
use App\Http\Controllers\Master\ItemPriceController;
use App\Http\Controllers\Master\ItemDepartmentController;
use App\Http\Controllers\Master\ItemSizeController;
use App\Http\Controllers\Master\ItemTypeController;
use App\Http\Controllers\Master\ItemVariantController;
use App\Http\Controllers\Master\ProgramLevelController;
use App\Http\Controllers\Master\StudyProgramController;
use App\Http\Controllers\Master\StockReceiveController;
use App\Http\Controllers\Master\VendorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\Auth\EmailVerificationOtpController;
use App\Http\Controllers\Auth\ForgotPasswordStudentController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Master\StudentController;
use App\Http\Controllers\Master\SizeMonitorController;
use App\Http\Controllers\Staff\ScanController;
use App\Http\Controllers\Student\SizeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'password.changed'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('master-data')->name('master-data.')->group(function () {
    Route::resource('faculty', FacultyController::class);
    Route::resource('study-program', StudyProgramController::class);
    Route::resource('program-level', ProgramLevelController::class);

    Route::resource('item-category', ItemCategoryController::class);
    Route::resource('item-type', ItemTypeController::class);
    Route::resource('item-department', ItemDepartmentController::class);
    Route::resource('item-size', ItemSizeController::class);

    Route::resource('item', ItemController::class);
    Route::post('item/{item}/variant', [ItemVariantController::class, 'store'])->name('item.variant.store');
    Route::delete('item/{item}/variant/{variant}', [ItemVariantController::class, 'destroy'])->name('item.variant.destroy');

    Route::resource('vendor', VendorController::class);
    Route::resource('item-price', ItemPriceController::class);
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('admin/students')->name('students.')->group(function () {
    Route::resource('/', StudentController::class)->parameters(['' => 'student']);
    Route::post('/generate', [StudentController::class, 'generate'])->name('generate');
    Route::post('/generate-all', [StudentController::class, 'generateAll'])->name('generateAll');
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('distribution')->name('distribution.')->group(function () {
    Route::resource('entitlement', EntitlementController::class);
    Route::resource('distribution-schedule', DistributionScheduleController::class);
    Route::get('size-monitor', [SizeMonitorController::class, 'index'])->name('size-monitor.index');
    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
    Route::post('/search', [ScanController::class, 'search'])->name('search');
    Route::get('/search', function () {
        return redirect()->route('distribution.scan.index');
    });
    Route::post('/process', [ScanController::class, 'process'])->name('process');
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('inventory')->name('inventory.')->group(function () {
    Route::resource('stock-receive', StockReceiveController::class)->except(['edit', 'update']);
    Route::resource('stock-opname', StockOpnameController::class)->except(['edit', 'update', 'destroy']);
    Route::post('stock-opname/{stockOpname}/upload', [StockOpnameController::class, 'upload'])->name('stock-opname.upload');
    Route::post('stock-opname/{stockOpname}/approve', [StockOpnameController::class, 'approve'])->name('stock-opname.approve');
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('report')->name('report.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('distribution', [ReportController::class, 'distribution'])->name('distribution');
    Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
    Route::get('gpm', [ReportController::class, 'gpm'])->name('gpm');
    Route::get('stock', [ReportController::class, 'stock'])->name('stock');
    Route::get('stock-opname', [ReportController::class, 'stockOpname'])->name('stock-opname');
    Route::get('stock-card', [ReportController::class, 'stockCard'])->name('stock-card');
    Route::get('loss', [ReportController::class, 'loss'])->name('loss');
    Route::get('gpm-cost', [GpmController::class, 'index'])->name('gpm-cost');
    Route::get('size-recap', [ReportController::class, 'sizeRecap'])->name('size-recap');
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->group(function () {
    Route::get('/templates/{type}/download', [TemplateController::class, 'download'])->name('templates.download');
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('import')->name('import.')->group(function () {
    Route::get('/', [ImportController::class, 'index'])->name('index');
    Route::post('/', [ImportController::class, 'store'])->name('store');
    Route::post('/preview', [ImportController::class, 'preview'])->name('preview');
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('eligibility', [EligibilityController::class, 'index'])->name('eligibility.index');
    Route::post('eligibility/{student}/toggle', [EligibilityController::class, 'toggle'])->name('eligibility.toggle');
});

Route::middleware(['auth', 'password.changed', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/sizes', [SizeController::class, 'index'])->name('sizes.index');
    Route::post('/sizes', [SizeController::class, 'store'])->name('sizes.store');
    Route::post('/email/send-otp', [EmailVerificationOtpController::class, 'sendOtp'])->name('email.send-otp');
    Route::get('/email/verify', [EmailVerificationOtpController::class, 'showVerifyForm'])->name('email.verify-form');
    Route::post('/email/verify-otp', [EmailVerificationOtpController::class, 'verifyOtp'])->name('email.verify-otp');
    Route::get('/qr', [SizeController::class, 'qr'])->name('qr');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [PasswordChangeController::class, 'create'])->name('password.change');
    Route::post('/password/change', [PasswordChangeController::class, 'store'])->name('password.change.store');
});

Route::get('/forgot-password/student', [ForgotPasswordStudentController::class, 'create'])->name('password.student.forgot');
Route::post('/forgot-password/student/reset', [ForgotPasswordStudentController::class, 'sendResetLink'])->name('password.student.send-reset');

require __DIR__.'/auth.php';
