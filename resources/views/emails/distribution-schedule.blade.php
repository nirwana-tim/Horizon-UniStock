<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Jadwal Pengambilan Seragam</title>
<style>
    body { margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, Helvetica, sans-serif; }
    .container { max-width: 600px; margin: 0 auto; padding: 24px; }
    .card { background: #ffffff; border-radius: 12px; overflow: hidden; }
    .header { background-color: #980416; padding: 24px; text-align: center; }
    .header h1 { color: #ffffff; margin: 0; font-size: 20px; }
    .body { padding: 24px; color: #374151; line-height: 1.6; }
    .body h2 { color: #111827; font-size: 18px; margin: 0 0 8px; }
    .body p { margin: 0 0 16px; }
    .info-box { background: #fdf2f3; border: 1px solid #fce7e9; border-radius: 8px; padding: 16px; margin: 16px 0; }
    .info-box .row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #fce7e9; }
    .info-box .row:last-child { border-bottom: none; }
    .info-box .label { color: #6b7280; font-size: 13px; }
    .info-box .value { color: #111827; font-weight: 600; text-align: right; }
    .button { display: inline-block; background-color: #980416; color: #ffffff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 16px 0; }
    .footer { padding: 16px 24px; text-align: center; color: #9ca3af; font-size: 12px; background: #f9fafb; }
    .warning { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px; color: #92400e; font-size: 13px; margin: 16px 0; }
</style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="header">
            <h1>Jadwal Pengambilan Seragam</h1>
        </div>
        <div class="body">
            <h2>Halo, {{ $student->name }}! 👋</h2>
            <p>Informasi jadwal pengambilan seragam Anda sudah tersedia:</p>

            <div class="info-box">
                <div class="row"><span class="label">Jadwal</span><span class="value">{{ $schedule->name }}</span></div>
                <div class="row"><span class="label">Tanggal</span><span class="value">{{ \Carbon\Carbon::parse($schedule->date)->format('d M Y') }}</span></div>
                @if($schedule->location)
                <div class="row"><span class="label">Lokasi</span><span class="value">{{ $schedule->location }}</span></div>
                @endif
                @if($schedule->session)
                <div class="row"><span class="label">Sesi</span><span class="value">{{ $schedule->session }}</span></div>
                @endif
                @if($schedule->period)
                <div class="row"><span class="label">Periode</span><span class="value">{{ $schedule->period }}</span></div>
                @endif
            </div>

            <p><strong>Yang perlu Anda bawa:</strong></p>
            <ol class="steps" style="padding-left: 20px; margin: 0 0 16px;">
                <li>QR Code identitas (dapat diunduh di menu <strong>Scan</strong> di aplikasi)</li>
                <li>Kartu Tanda Mahasiswa (jika ada)</li>
            </ol>

            <p style="text-align:center">
                <a href="{{ url('/student/qr') }}" class="button">Lihat QR Code Saya</a>
            </p>

            <div class="warning">
                <strong>Catatan:</strong> Pastikan ukuran seragam Anda sudah diisi di menu <strong>Ukuran</strong> sebelum datang ke lokasi.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Horizon') }} — Sistem Penerimaan Barang Mahasiswa
        </div>
    </div>
</div>
</body>
</html>