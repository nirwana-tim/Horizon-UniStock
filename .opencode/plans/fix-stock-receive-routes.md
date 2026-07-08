# Fix: Stock Receive Create — Route Ordering

## Masalah
Route `search-items` dan `variants-by-item/{item}` didefinisikan SETELAH `Route::resource('stock-receive', ...)`. Karena `Route::resource` membuat route `GET stock-receive/{stock_receive}` (show) yang bersifat catch-all, endpoint AJAX tidak pernah tercapai — selalu kena 404 (route model binding gagal).

## Fix

### File: `routes/web.php`

**Before (line 95-102):**
```php
Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('inventory')->name('inventory.')->group(function () {
    Route::resource('stock-receive', StockReceiveController::class)->except(['edit', 'update']);
    Route::get('stock-receive/search-items', [StockReceiveController::class, 'searchItems'])->name('stock-receive.search-items');
    Route::get('stock-receive/variants-by-item/{item}', [StockReceiveController::class, 'variantsByItem'])->name('stock-receive.variants-by-item');
    Route::resource('stock-opname', StockOpnameController::class)->except(['edit', 'update', 'destroy']);
    ...
});
```

**After:**
```php
Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('inventory')->name('inventory.')->group(function () {
    Route::get('stock-receive/search-items', [StockReceiveController::class, 'searchItems'])->name('stock-receive.search-items');
    Route::get('stock-receive/variants-by-item/{item}', [StockReceiveController::class, 'variantsByItem'])->name('stock-receive.variants-by-item');
    Route::resource('stock-receive', StockReceiveController::class)->except(['edit', 'update']);
    Route::resource('stock-opname', StockOpnameController::class)->except(['edit', 'update', 'destroy']);
    ...
});
```

**Perubahan:** Pindahkan baris `Route::get('stock-receive/search-items', ...)` dan `Route::get('stock-receive/variants-by-item/{item}', ...)` DARI setelah resource MENJADI sebelum resource.

### Urutan route setelah fix:
```
 1. GET  stock-receive/search-items               ← custom, dicek duluan
 2. GET  stock-receive/variants-by-item/{item}     ← custom, dicek kedua
 3. GET  stock-receive/create                      ← resource
 4. GET  stock-receive/{stock_receive}             ← show (fallback)
 5. POST stock-receive                             ← store
 6. GET  stock-receive                             ← index
 7. DELETE stock-receive/{stock_receive}           ← destroy
```

### Verifikasi
Jalankan setelah fix:
```
php artisan route:list --path="inventory/stock-receive"
```

Pastikan `search-items` dan `variants-by-item` muncul SEBELUM `{stock_receive}` dalam daftar.
