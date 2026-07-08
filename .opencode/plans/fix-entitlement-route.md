# Fix: Entitlement Create/Edit — Route Ordering (same pattern as stock-receive)

## Masalah
`items-grid` didefinisikan SETELAH `Route::resource('entitlement', ...)`, jadi route `{entitlement}` (show) men-shadow `items-grid`. AJAX call dari halaman create & edit return 404.

## Fix

### File: `routes/web.php` (baris 77-84)

**Before:**
```php
Route::resource('entitlement', EntitlementController::class);
Route::get('entitlement/items-grid', [EntitlementController::class, 'itemsGrid'])->name('entitlement.items-grid');
```

**After:**
```php
Route::get('entitlement/items-grid', [EntitlementController::class, 'itemsGrid'])->name('entitlement.items-grid');
Route::resource('entitlement', EntitlementController::class);
```

## Verifikasi
```
php artisan route:list --path="distribution/entitlement"
```
