<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Peringatan Stok Kurang</title>
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
    .note { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px; color: #92400e; font-size: 13px; margin: 16px 0; }
    .footer { padding: 16px 24px; text-align: center; color: #9ca3af; font-size: 12px; background: #f9fafb; }
</style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="header">
            <h1>Peringatan Stok Kurang</h1>
        </div>
        <div class="body">
            <h2>Halo Admin, ⚠️</h2>

            <p>
                Terdeteksi distribusi dengan status <strong>Sebagian (partial)</strong> karena stok
                barang tidak mencukupi. Berikut detailnya:
            </p>

            <div class="info-box">
                <div class="row"><span class="label">Mahasiswa</span><span class="value">{{ $transaction->student?->name ?? '-' }} ({{ $transaction->student?->nim ?? '-' }})</span></div>
                <div class="row"><span class="label">Jadwal</span><span class="value">{{ $transaction->schedule?->name ?? '-' }}</span></div>
                <div class="row"><span class="label">Waktu</span><span class="value">{{ \Carbon\Carbon::parse($transaction->pickup_time)->format('d M Y H:i') }}</span></div>
                <div class="row"><span class="label">Status</span><span class="value">Partial / Sebagian</span></div>
            </div>

            @if($transaction->notes)
                <div class="note">
                    <strong>Catatan:</strong> {{ $transaction->notes }}
                </div>
            @endif

            <p>Silakan segera lakukan pengecekan dan penambahan stok untuk kelancaran distribusi berikutnya.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Horizon') }} — Sistem Penerimaan Barang Mahasiswa
        </div>
    </div>
</div>
</body>
</html>
