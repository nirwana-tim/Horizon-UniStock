# Spatie Laravel Permission

## Apa Itu?

Package manajemen role & permission untuk Laravel. Setiap user bisa punya banyak role dan permission.

## Fitur yg Terinstall

| Fitur | Untuk Apa |
|-------|-----------|
| Role | Grup permission (super_admin, admin, staff, student) |
| Permission | Izin spesifik (manage-students, manage-distributions, manage-finance) |
| Direct Permission | Beri izin langsung ke user tanpa role |
| Blade Directives | `@role`, `@hasrole`, `@can`, `@cannot` di view |
| Middleware | `role:`, `permission:` di route |
| Gate Integration | Pake `@can()` kayak Laravel biasa |
| Seeder | Bikin role/permission via DatabaseSeeder |
| Super Admin | User dg role super_admin bypass semua permission check |

## 1. Setup di User Model

**`app/Models/User.php`**
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    // ...
}
```

## 2. Buat Role & Permission

**Contoh generic (mirip official docs):**
```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Buat permission
Permission::create(['name' => 'edit articles']);
Permission::create(['name' => 'publish articles']);

// Buat role dan assign permission
$role = Role::create(['name' => 'writer']);
$role->givePermissionTo('edit articles');

$admin = Role::create(['name' => 'admin']);
$admin->givePermissionTo(Permission::all());

// Assign role ke user
$user = User::find(1);
$user->assignRole('admin');
```

> **Untuk implementasi project ini**, lihat `database/seeders/RolePermissionSeeder.php`:

```php
// Role & permission aktual UniStock
Permission::firstOrCreate(['name' => 'manage-students']);
Permission::firstOrCreate(['name' => 'manage-distributions']);
Permission::firstOrCreate(['name' => 'manage-finance']);

$superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
$superAdmin->givePermissionTo(Permission::all());

$admin = Role::firstOrCreate(['name' => 'admin']);
$admin->givePermissionTo(['manage-finance', 'manage-distributions']);

$staff = Role::firstOrCreate(['name' => 'staff']);
$staff->givePermissionTo('manage-students');

Role::firstOrCreate(['name' => 'student']);
```

## 3. Middleware di Route

Proyek ini memakai middleware **`role:`** di semua route terproteksi. Middleware `permission:` **tidak digunakan** di route — permission Spatie hanya untuk role assignment & blade directives.

```php
Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('master-data')->name('master-data.')->group(function () {
    Route::resource('faculty', FacultyController::class);
    // ...
});

// Role campuran: staff juga boleh akses scan
Route::middleware('role:super_admin|admin|staff')->group(function () {
    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
});
```

Registrasi middleware di `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
    ]);
})
```

## 4. Blade Directive

```blade
@role('super_admin')
    <a href="/admin">Panel Admin</a>
@endrole

@hasrole('admin')
    <a href="/report">Laporan</a>
@endhasrole

@can('manage-students')
    <button>Tambah Mahasiswa</button>
@endcan

@cannot('manage-finance')
    <p>Maaf, akses ditolak</p>
@endcannot
```

## 5. Cek di Controller

```php
// Cek role
$user->hasRole('student');
$user->hasAnyRole(['staff', 'admin']);

// Cek permission
$user->hasPermissionTo('manage-students');
$user->hasAnyPermission(['manage-finance', 'manage-students']);

// Assign
$user->assignRole('super_admin');
$user->givePermissionTo('manage-students');

// Remove
$user->removeRole('student');
$user->revokePermissionTo('manage-students');
```

## 6. Super Admin Bypass (Gate::before)

Official Spatie recommendation: gunakan `Gate::before()` di `AppServiceProvider` agar user dengan role `super_admin` otomatis punya semua permission tanpa perlu di-assign satu-satu.

**`app/Providers/AppServiceProvider.php`**
```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });
    }
}
```

> **Penting:** `Gate::before()` harus return `null` (bukan `false`) jika user bukan super_admin, agar policy normal tetap jalan.

### Cara kerja:
- Semua `@can()`, `$user->can()`, dan middleware `permission:` akan return `true` untuk super_admin
- Pengecualian: `$user->hasPermissionTo()` langsung (tidak lewat Gate) — untuk itu super_admin tetap perlu permission di-assign ke role-nya

## 7. Tabel Database

| Tabel | Isi |
|-------|-----|
| `permissions` | Daftar semua permission |
| `roles` | Daftar role |
| `model_has_roles` | Relasi user → role |
| `model_has_permissions` | Relasi user → permission langsung |
| `role_has_permissions` | Relasi role → permission |

## Sumber
- https://spatie.be/docs/laravel-permission/v8
- https://spatie.be/docs/laravel-permission/v8/basic-usage/super-admin
- https://github.com/spatie/laravel-permission

## Analogi
Spatie Permission itu seperti KTP + SIM. Role = SIM (jenis kendaraan yg boleh dikendarai). Permission = peraturan jalan (boleh belok kiri, boleh parkir). Super Admin = polisi yg boleh melanggar semua aturan — diatur via `Gate::before()`.
