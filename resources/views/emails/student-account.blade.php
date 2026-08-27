<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Akun {{ config('app.name', 'Horizon') }} Anda</title>
<style>
    body { margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, Helvetica, sans-serif; }
    .container { max-width: 600px; margin: 0 auto; padding: 24px; }
    .card { background: #ffffff; border-radius: 12px; overflow: hidden; }
    .header { background-color: #980416; padding: 24px; text-align: center; }
    .header h1 { color: #ffffff; margin: 0; font-size: 20px; }
    .body { padding: 24px; color: #374151; line-height: 1.6; }
    .body h2 { color: #111827; font-size: 18px; margin: 0 0 8px; }
    .body p { margin: 0 0 16px; }
    .credential-box { background: #fdf2f3; border: 1px solid #fce7e9; border-radius: 8px; padding: 16px; margin: 16px 0; }
    .credential-box .row { display: flex; justify-content: space-between; padding: 6px 0; }
    .credential-box .label { color: #6b7280; font-size: 13px; }
    .credential-box .value { color: #111827; font-weight: 600; }
    .button { display: inline-block; background-color: #980416; color: #ffffff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 16px 0; }
    .steps { margin: 16px 0; padding-left: 20px; }
    .steps li { margin-bottom: 8px; }
    .footer { padding: 16px 24px; text-align: center; color: #9ca3af; font-size: 12px; background: #f9fafb; }
    .warning { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px; color: #92400e; font-size: 13px; margin: 16px 0; }
</style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="header">
            <h1>{{ config('app.name', 'Horizon') }}</h1>
        </div>
        <div class="body">
            <h2>Halo, {{ $student->name }}! 👋</h2>
            <p>Akun Anda sudah siap digunakan di sistem {{ config('app.name', 'Horizon') }}.</p>

            <div class="credential-box">
                <div class="row"><span class="label">NIM</span><span class="value">{{ $student->nim }}</span></div>
                <div class="row"><span class="label">Email</span><span class="value">{{ $student->email_kampus ?? $student->nim . '@temp.horizon.ac.id' }}</span></div>
                <div class="row"><span class="label">Password</span><span class="value">{{ $password }}</span></div>
            </div>

            <p>Langkah selanjutnya:</p>
            <ol class="steps">
                <li>Buka <strong>{{ url('/login') }}</strong></li>
                <li>Login menggunakan NIM: <strong>{{ $student->nim }}</strong></li>
                <li>Password: <strong>{{ $password }}</strong></li>
                <li>Anda akan diminta mengganti password setelah login pertama kali</li>
                <li>Isi ukuran seragam di menu <strong>Ukuran</strong></li>
            </ol>

            <p style="text-align:center">
                <a href="{{ url('/login') }}" class="button">Masuk ke Sistem</a>
            </p>

            <div class="warning">
                <strong>Perhatian:</strong> Password ini hanya dikirim sekali. Simpan dengan aman. Jangan bagikan kepada siapa pun.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Horizon') }} — Sistem Penerimaan Barang Mahasiswa
        </div>
    </div>
</div>
</body>
</html>