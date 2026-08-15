# Breeze (Auth Scaffolding)

## Apa Itu Breeze?

Breeze adalah starter kit minimalis untuk login, register, forgot/reset password, email verification, dan profile management. Terinstal dengan Blade + Alpine.js + Tailwind CSS.

## Fitur yg Terinstall

| Fitur | Untuk Apa |
|-------|-----------|
| Login | Halaman + logic autentikasi (POST /login, `throttle:5,1`) |
| Register | **Nonaktif** di proyek ini (akun dibuat Admin / otomatis) |
| Logout | Hapus session (POST /logout) |
| Forgot Password | Kirim link reset ke email (`throttle:3,1`) |
| Reset Password | Ganti password via token (`throttle:3,1`) |
| Email Verification | Breeze bawaan **tidak dipakai** — verifikasi email kampus pakai OTP custom |
| Profile Update | Edit nama & email di halaman profile |
| Change Password | Ganti password dari dalam aplikasi (`password.change` untuk first-login) |

## Routes yg Tersedia

| Method | URI | Middleware | Untuk |
|--------|-----|-----------|-------|
| GET | `/login` | guest | Tampil form login |
| POST | `/login` | guest, `throttle:5,1` | Proses login |
| POST | `/logout` | auth | Logout |
| GET | `/forgot-password` | guest | Form minta reset link |
| POST | `/forgot-password` | guest, `throttle:3,1` | Kirim reset link |
| GET | `/reset-password/{token}` | guest | Form reset password |
| POST | `/reset-password` | guest, `throttle:3,1` | Proses reset |
| GET | `/dashboard` | auth, `password.changed` | Halaman dashboard |
| GET | `/password/change` | auth | Form ganti password wajib (first login) |
| POST | `/password/change` | auth, `throttle:5,1` | Proses ganti password wajib |
| PUT | `/password` | auth, `throttle:5,1` | Update password di profile |
| GET | `/profile` | auth | Edit profile |
| PATCH | `/profile` | auth, `throttle:5,1` | Update profile |
| DELETE | `/profile` | auth, `throttle:5,1` | Hapus akun |
| GET/POST | `/user/confirm-password` | auth | Konfirmasi password |

> ⚠️ Breeze Blade Stack **tidak menggunakan Fortify** — auth logic ada di `app/Http/Controllers/Auth/`, bukan `app/Actions/Fortify/`.
>
> ⚠️ **Register nonaktif** — tidak ada route `/register` (`RegisteredUserController` tidak diregistrasi di `routes/auth.php`). Akun mahasiswa dibuat via fitur "Generate Akun" Admin.
>
> ⚠️ **`password.changed`** — middleware custom yang me-redirect ke `/password/change` jika `users.must_change_password = true`. Semua route (kecuali dashboard & password/change) memakainya.

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

## 3. Registrasi Akun (bukan Register Breeze)

Register publik nonaktif. Akun dibuat lewat:

- **Generate Akun Mahasiswa** (`StudentController::generate`) — user = NIM, password random, `must_change_password = true`.
- **Kelola User** (`System\UserController` di `/admin/users`) — untuk admin/staff.
- **Seeder** — `SuperadminSeeder` (super_admin), `UserTestSeeder`, `FakeDataSeeder`.

## 4. Verifikasi Email Kampus (OTP, bukan Breeze)

Verifikasi email bawaan Breeze (`MustVerifyEmail`) tidak digunakan. Proyek memakai **OTP 6 digit** ke email kampus:

- `Profile\EmailController` — ganti email kampus user (verify password → input email → send OTP → verify).
- `Student\EmailVerificationOtpController` — verifikasi email kampus mahasiswa (`/student/email/*`).
- Kode OTP tersimpan (hash) di tabel `otp_codes`, kedaluwarsa 15 menit.

## 5. Struktur File

```
app/
├── Http/Controllers/      # ProfileController + Auth controllers (+ PasswordChangeController)
└── View/                  # Components (AppLayout, GuestLayout)
resources/views/
├── layouts/               # app.blade.php, guest.blade.php
├── components/            # input-error, nav-link, primary-button, sidebar, bottom-nav, dll
├── auth/                  # login, forgot-password, reset-password, change-password
├── profile/               # update-profile-information-form, update-password-form, email change
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
