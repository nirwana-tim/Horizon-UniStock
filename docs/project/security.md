# Security Design

Implementasi keamanan:

- [x] Password hashing (bcrypt)
- [x] Role middleware (Spatie Permission)
- [x] Permission check per menu/aksi
- [x] CSRF protection
- [x] Server-side validation
- [x] Audit log setiap perubahan data
- [x] QR menggunakan token random (bukan NIM)
- [x] Mahasiswa hanya melihat data sendiri
- [x] Staff tidak melihat data sensitif Admin
- [x] Super Admin hanya diakses oleh akun tertentu
- [x] Email OTP kedaluwarsa (15 menit)
- [x] Rate limiting pada login & OTP

## Detail

### Autentikasi
- Login menggunakan email + password (Breeze)
- Password di-hash dengan bcrypt
- Rate limiting: maks 3x percobaan login, akun terkunci 15 menit
- First login wajib ganti password

### Otorisasi
- Role-based access control via Spatie Permission
- 4 role: `super_admin`, `finance`, `staff`, `student`
- Middleware `role:` dan `permission:` di route
- Blade directives (`@role`, `@can`) di view
- Super Admin bypass semua permission via `Gate::before()`

### QR Security
- QR menggunakan UUID token (bukan NIM)
- Token digenerate 1x seumur hidup
- QR tidak mengandung data sensitif

### Audit
- Semua perubahan data tercatat di `audit_logs`
- Log: siapa, aksi, model, data sebelum/sesudah, IP address
- Import activity tercatat di `import_batches`

### Lupa Password
- OTP 6 digit (A-Z, a-z, 0-9) dikirim ke email kampus
- OTP kedaluwarsa 15 menit
- Rate limiting pada pengiriman OTP

### Anti Duplikat
- Sistem tolak double submit transaksi
- Email notifikasi hanya dikirim 1x per mahasiswa per jadwal
- Import duplicate NIM ditolak
