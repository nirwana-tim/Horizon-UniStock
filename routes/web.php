<?php

use App\Http\Controllers\Finance\StockOpnameController;
use App\Http\Controllers\Finance\GpmController;
use App\Http\Controllers\Master\FacultyController;
use App\Http\Controllers\Master\StudyProgramController;
use App\Http\Controllers\Master\ProgramLevelController;
use App\Http\Controllers\Master\ItemCategoryController;
use App\Http\Controllers\Master\ItemController;
use App\Http\Controllers\Master\ItemVariantController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Staff\ScanController;
use App\Http\Controllers\Student\SizeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:super_admin,finance'])->prefix('master')->name('master.')->group(function () {
    Route::resource('faculty', FacultyController::class);
    Route::resource('study-program', StudyProgramController::class);
    Route::resource('program-level', ProgramLevelController::class);
    Route::resource('item-category', ItemCategoryController::class);

    Route::resource('item', ItemController::class);
    Route::post('item/{item}/variant', [ItemVariantController::class, 'store'])->name('item.variant.store');
    Route::delete('item/{item}/variant/{variant}', [ItemVariantController::class, 'destroy'])->name('item.variant.destroy');
});

Route::middleware(['auth', 'role:super_admin,finance'])->prefix('import')->name('import.')->group(function () {
    Route::get('/', [ImportController::class, 'index'])->name('index');
    Route::post('/', [ImportController::class, 'store'])->name('store');
    Route::post('/preview', [ImportController::class, 'preview'])->name('preview');
});

Route::middleware(['auth', 'role:super_admin,finance'])->prefix('finance')->name('finance.')->group(function () {
    Route::resource('stock-opname', StockOpnameController::class)->except(['edit', 'update', 'destroy']);
    Route::post('stock-opname/{stockOpname}/upload', [StockOpnameController::class, 'upload'])->name('stock-opname.upload');
    Route::post('stock-opname/{stockOpname}/approve', [StockOpnameController::class, 'approve'])->name('stock-opname.approve');
    Route::get('gpm', [GpmController::class, 'index'])->name('gpm.index');
});

Route::middleware(['auth', 'role:super_admin,finance'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('distribution', [ReportController::class, 'distribution'])->name('distribution');
    Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
    Route::get('gpm', [ReportController::class, 'gpm'])->name('gpm');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/sizes', [SizeController::class, 'index'])->name('sizes.index');
    Route::post('/sizes', [SizeController::class, 'store'])->name('sizes.store');
    Route::get('/qr', [SizeController::class, 'qr'])->name('qr');
});

Route::middleware(['auth', 'role:staff,finance'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
    Route::post('/search', [ScanController::class, 'search'])->name('search');
    Route::post('/process', [ScanController::class, 'process'])->name('process');
});

require __DIR__.'/auth.php';
