<?php

use App\Http\Controllers\Finance\StockOpnameController;
use App\Http\Controllers\Finance\GpmController;
use App\Http\Controllers\Finance\EligibilityController;
use App\Http\Controllers\Finance\SizeChangeEventController;
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
use App\Http\Controllers\Master\StudentGenerationController;
use App\Http\Controllers\Master\StudyProgramController;
use App\Http\Controllers\Master\StockReceiveController;
use App\Http\Controllers\Inventory\StockBalanceController;
use App\Http\Controllers\Inventory\StockMovementController;
use App\Http\Controllers\Master\VendorController;
use App\Http\Controllers\Master\StudentLevelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Profile\EmailController as ProfileEmailController;
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

Route::get('/dashboard', DashboardController::class)->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'password.changed'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('profile/email')->name('profile.email.')->group(function () {
        Route::get('/change', [ProfileEmailController::class, 'showChangeForm'])->name('change');
        Route::post('/verify-password', [ProfileEmailController::class, 'verifyPassword'])->name('verify-password');
        Route::get('/input-email', [ProfileEmailController::class, 'showEmailForm'])->name('input-email');
        Route::post('/send-otp', [ProfileEmailController::class, 'sendOtp'])->middleware('throttle:3,1')->name('send-otp');
        Route::get('/verify', [ProfileEmailController::class, 'showOtpForm'])->name('verify-otp');
        Route::post('/verify', [ProfileEmailController::class, 'verifyOtp'])->middleware('throttle:5,1')->name('verify-otp.post');
    });
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('master-data')->name('master-data.')->group(function () {
    Route::resource('faculty', FacultyController::class);
    Route::resource('study-program', StudyProgramController::class);
    Route::resource('student-generation', StudentGenerationController::class);

    Route::resource('item-category', ItemCategoryController::class);
    Route::resource('item-type', ItemTypeController::class);
    Route::resource('item-department', ItemDepartmentController::class);
    Route::resource('item-size', ItemSizeController::class);

    Route::get('item/sizes-types-by-category', [ItemController::class, 'sizesTypesByCategory'])->name('item.sizes-types-by-category');
    Route::resource('item', ItemController::class);
    Route::post('item/{item}/variant', [ItemVariantController::class, 'store'])->name('item.variant.store');
    Route::delete('item/{item}/variant/{variant}', [ItemVariantController::class, 'destroy'])->name('item.variant.destroy');

    Route::resource('vendor', VendorController::class);
    Route::resource('item-price', ItemPriceController::class);
    Route::resource('student-level', StudentLevelController::class)->only(['index', 'show']);
});



Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('distribution')->name('distribution.')->group(function () {
    Route::get('entitlement/items-grid', [EntitlementController::class, 'itemsGrid'])->name('entitlement.items-grid');
    Route::resource('entitlement', EntitlementController::class);
    Route::get('distribution-schedule/fetch-items', [DistributionScheduleController::class, 'fetchItems'])->name('distribution-schedule.fetch-items');
    Route::get('distribution-schedule/{distributionSchedule}/transactions', [DistributionScheduleController::class, 'transactions'])->name('distribution-schedule.transactions');
    Route::resource('distribution-schedule', DistributionScheduleController::class);
    Route::get('size-monitor', [SizeMonitorController::class, 'index'])->name('size-monitor.index');
    Route::resource('size-events', SizeChangeEventController::class);
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin|staff'])->prefix('distribution')->name('distribution.')->group(function () {
    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
    Route::get('/student/{nim}', [ScanController::class, 'showByNim'])->name('scan.student');
    Route::post('/search', [ScanController::class, 'search'])->middleware('throttle:30,1')->name('search');
    Route::get('/search', function (Request $request) {
        $nim = $request->query('query');
        if ($nim) {
            return redirect()->route('distribution.scan.student', $nim);
        }
        return redirect()->route('distribution.scan.index');
    });
    Route::post('/process', [ScanController::class, 'process'])->middleware('throttle:10,1')->name('process');
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('inventory')->name('inventory.')->group(function () {
    Route::get('stock-receive/search-items', [StockReceiveController::class, 'searchItems'])->name('stock-receive.search-items');
    Route::get('stock-receive/variants-by-item/{item}', [StockReceiveController::class, 'variantsByItem'])->name('stock-receive.variants-by-item');
    Route::get('stock-receive/variants-by-base-code/{baseCode}', [StockReceiveController::class, 'variantsByBaseCode'])->name('stock-receive.variants-by-base-code')->where('baseCode', '.*');
    Route::resource('stock-receive', StockReceiveController::class)->except(['edit', 'update']);
    Route::get('stock-balance', [StockBalanceController::class, 'index'])->name('stock-balance.index');
    Route::get('stock-movement', [StockMovementController::class, 'index'])->name('stock-movement.index');
    Route::resource('stock-opname', StockOpnameController::class)->except(['edit', 'update', 'destroy']);
    Route::post('stock-opname/{stockOpname}/upload', [StockOpnameController::class, 'upload'])->middleware('throttle:5,1')->name('stock-opname.upload');
    Route::post('stock-opname/{stockOpname}/approve', [StockOpnameController::class, 'approve'])->middleware('throttle:5,1')->name('stock-opname.approve');
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('report')->name('report.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('distribution', [ReportController::class, 'distribution'])->name('distribution');
    Route::get('distribution-recap', [ReportController::class, 'distributionRecap'])->name('distribution-recap');
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
    Route::get('/{importBatch}', [ImportController::class, 'result'])->name('result');
    Route::post('/', [ImportController::class, 'store'])->middleware('throttle:5,1')->name('store');
    Route::post('/preview', [ImportController::class, 'preview'])->middleware('throttle:10,1')->name('preview');
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('student')->name('finance.eligibility.')->group(function () {
    Route::get('/eligibility', [EligibilityController::class, 'index'])->name('index');
    Route::post('/eligibility/{student}/toggle', [EligibilityController::class, 'toggle'])->middleware('throttle:10,1')->name('toggle');
});

Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('student')->name('students.')->group(function () {
    Route::get('/students-data', [StudentController::class, 'index'])->name('index');
    Route::get('/students-data/create', [StudentController::class, 'create'])->name('create');
    Route::post('/students-data', [StudentController::class, 'store'])->name('store');
    Route::get('/students-data/{student}', [StudentController::class, 'show'])->name('show');
    Route::get('/students-data/{student}/edit', [StudentController::class, 'edit'])->name('edit');
    Route::put('/students-data/{student}', [StudentController::class, 'update'])->name('update');
    Route::delete('/students-data/{student}', [StudentController::class, 'destroy'])->name('destroy');
    Route::get('/students-data/{student}/entitlement', [StudentController::class, 'entitlement'])->name('entitlement');
    Route::get('/students-data/{student}/received-items', [StudentController::class, 'receivedItems'])->name('received-items');
    Route::get('/students-data/{student}/transactions', [StudentController::class, 'transactions'])->name('transactions');
    Route::patch('/students-data/{student}/toggle-status', [StudentController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/generate', [StudentController::class, 'generateIndex'])->name('generate-index');
    Route::post('/generate', [StudentController::class, 'generate'])->middleware('throttle:5,1')->name('generate');
    Route::post('/generate-all', [StudentController::class, 'generateAll'])->middleware('throttle:2,1')->name('generateAll');
    Route::get('/export', [StudentController::class, 'export'])->name('export');
    Route::get('/promote', [StudentController::class, 'promoteForm'])->name('promote.form');
    Route::post('/promote', [StudentController::class, 'promote'])->name('promote');
});

Route::middleware(['auth', 'password.changed', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/sizes', [SizeController::class, 'index'])->name('sizes.index');
    Route::get('/sizes/{event}/input', [SizeController::class, 'input'])->name('sizes.input');
    Route::post('/sizes', [SizeController::class, 'store'])->name('sizes.store');
    Route::post('/email/send-otp', [EmailVerificationOtpController::class, 'sendOtp'])->middleware('throttle:3,1')->name('email.send-otp');
    Route::get('/email/verify', [EmailVerificationOtpController::class, 'showVerifyForm'])->name('email.verify-form');
    Route::post('/email/verify-otp', [EmailVerificationOtpController::class, 'verifyOtp'])->middleware('throttle:5,1')->name('email.verify-otp');
    Route::get('/qr', [SizeController::class, 'qr'])->name('qr');
    Route::get('/items', [SizeController::class, 'items'])->name('items.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [PasswordChangeController::class, 'create'])->name('password.change');
    Route::post('/password/change', [PasswordChangeController::class, 'store'])->middleware('throttle:5,1')->name('password.change.store');
});

Route::get('/forgot-password/student', [ForgotPasswordStudentController::class, 'create'])->name('password.student.forgot');
Route::post('/forgot-password/student/reset', [ForgotPasswordStudentController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('password.student.send-reset');

require __DIR__.'/auth.php';
