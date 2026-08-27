# Security Design

Implementasi keamanan:

- [x] Password hashing (bcrypt)
- [x] Role middleware (Spatie Permission)
- [x] CSRF protection
- [x] Server-side validation (Form Request)
- [x] QR berisi NIM (identitas permanen), bukan token random
- [x] Mahasiswa hanya melihat data sendiri
- [x] Staff tidak melihat data sensitif Admin
- [x] Super Admin hanya diakses oleh akun tertentu
- [x] Email OTP kedaluwarsa (15 menit)
- [x] Rate limiting pada login & OTP
- [x] Wajib ganti password saat first login

## Detail

### Autentikasi
- Login menggunakan NIM/email + password (Breeze)
- Password di-hash dengan bcrypt
- Rate limiting: `throttle:5,1` pada login; `throttle:3,1` pada forgot & reset password (3x per menit — bukan "terkunci 15 menit")
- First login wajib ganti password (`must_change_password` → redirect ke `/password/change` via middleware `password.changed`)
- Endpoint form/aksi sensitif diberi throttle spesifik (mis. `throttle:5,1` untuk aksi, `throttle:30,1` untuk halaman)

### Otorisasi
- Role-based access control via Spatie Permission
- 4 role: `super_admin`, `admin`, `staff`, `student`
- Middleware `role:` di route (contoh `role:super_admin|admin`)
- Blade directives (`@role`, `@can`) di view
- Super Admin bypass semua permission via `Gate::before()`

### QR Security
- QR berisi NIM (bukan token random) — identitas permanen 1x seumur hidup
- QR di-generate sebagai PNG data URL via `bacon/bacon-qr-code`
- QR tidak mengandung data sensitif lain selain NIM

### Audit
- Import activity tercatat di `import_batches`

### Lupa Password
- OTP 6 digit dikirim ke email kampus (setelah verifikasi email)
- OTP kedaluwarsa 15 menit
- Rate limiting pada pengiriman OTP (`throttle:3,1`)

### Anti Duplikat
- Sistem tolak double submit transaksi (`distribution_transactions` unique `(student_id, schedule_id)`)
- Email notifikasi hanya dikirim 1x per mahasiswa per jadwal
- Import duplicate NIM ditolak
