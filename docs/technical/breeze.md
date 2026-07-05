# Breeze (Auth Scaffolding)

## Apa Itu Breeze?

Breeze adalah starter kit minimalis untuk login, register, forgot/reset password, email verification, dan profile management. Terinstal dengan Blade + Alpine.js + Tailwind CSS.

## Fitur yg Terinstall

| Fitur | Untuk Apa |
|-------|-----------|
| Login | Halaman + logic autentikasi (POST /login) |
| Register | Pendaftaran user baru (POST /register) |
| Logout | Hapus session (POST /logout) |
| Forgot Password | Kirim link reset ke email |
| Reset Password | Ganti password via token |
| Email Verification | Verifikasi alamat email (**nonaktif default**, perlu `MustVerifyEmail`) |
| Profile Update | Edit nama & email di halaman profile |
| Change Password | Ganti password dari dalam aplikasi |
| Dark Mode | Toggle tema gelap (opsional pas install) |

## Routes yg Tersedia

| Method | URI | Middleware | Untuk |
|--------|-----|-----------|-------|
| GET | `/login` | guest | Tampil form login |
| POST | `/login` | guest | Proses login |
| POST | `/logout` | auth | Logout |
| GET | `/register` | guest | Form register |
| POST | `/register` | guest | Proses register |
| GET | `/forgot-password` | guest | Form minta reset link |
| POST | `/forgot-password` | guest | Kirim reset link |
| GET | `/reset-password/{token}` | guest | Form reset password |
| POST | `/reset-password` | guest | Proses reset |
| GET | `/dashboard` | auth, verified | Halaman dashboard |
| GET | `/profile` | auth | Edit profile |
| PATCH | `/profile` | auth | Update profile |
| DELETE | `/profile` | auth | Hapus akun |
| GET | `/user/confirm-password` | auth | Konfirmasi password |
| POST | `/user/confirm-password` | auth | Proses konfirmasi |

> ⚠️ Breeze Blade Stack **tidak menggunakan Fortify** — auth logic ada di `app/Http/Controllers/Auth/`, bukan `app/Actions/Fortify/`. Untuk kustomisasi register, edit langsung `RegisteredUserController`.

## 1. Cek Login di Blade

```blade
@auth
    <p>Welcome, {{ Auth::user()->name }}</p>
@endauth

@guest
    <a href="/login">Login</a>
@endguest
```

## 2. Middleware di Route

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // route yg butuh email verified
});
```

## 3. Custom Logic Register

**`app/Http/Controllers/Auth/RegisteredUserController.php`** — edit langsung di controller ini:

```php
protected function create(array $input): User
{
    Validator::make($input, [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users'],
        'phone' => ['required', 'string', 'max:20'],
        'password' => ['required', 'confirmed', Password::defaults()],
    ])->validate();

    return User::create([
        'name' => $input['name'],
        'email' => $input['email'],
        'phone' => $input['phone'],
        'password' => Hash::make($input['password']),
    ]);
}
```

## 4. Aktifkan Email Verification

1. Buka `app/Models/User.php`
2. Uncomment `implements MustVerifyEmail`
3. Tambah `use Illuminate\Contracts\Auth\MustVerifyEmail;`
4. Route yang butuh verified tambah middleware `verified`

```php
class User extends Authenticatable implements MustVerifyEmail
{
    // ...
}
```

## 5. Struktur File Breeze

```
app/
├── Http/Controllers/      # ProfileController + Auth controllers
└── View/                  # Components (AppLayout, GuestLayout)
resources/views/
├── layouts/               # app.blade.php, guest.blade.php
├── components/            # input-error, nav-link, primary-button, sidebar, etc.
├── auth/                  # login, register, forgot-password, verify-email, dll
├── profile/               # update-profile-information-form, update-password-form
├── dashboards/            # Dashboard per role
├── master/                # Master data CRUD
├── distribution/          # Entitlement, jadwal, scan
├── inventory/             # Stock receive, stock opname
├── report/                # Laporan & GPM
├── student/               # Student self-service
├── import/                # Import data
└── welcome.blade.php
```

## Sumber
- https://laravel.com/docs/13.x/starter-kits
- https://github.com/laravel/breeze

## Analogi
Breeze itu seperti resepsionis gedung — dia yang urus siapa boleh masuk (login), daftar tamu (register), lupa kartu akses (forgot password), dan ganti foto KTP (profile update).
