<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Konfirmasi Pengambilan Seragam</title>
<style>
    body { margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, Helvetica, sans-serif; }
    .container { max-width: 600px; margin: 0 auto; padding: 24px; }
    .card { background: #ffffff; border-radius: 12px; overflow: hidden; }
    .header { background-color: #980416; padding: 24px; text-align: center; }
    .header h1 { color: #ffffff; margin: 0; font-size: 20px; }
    .body { padding: 24px; color: #374151; line-height: 1.6; }
    .body h2 { color: #111827; font-size: 18px; margin: 0 0 8px; }
    .body p { margin: 0 0 16px; }
    .status-badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 13px; font-weight: 700; }
    .status-completed { background: #dcfce7; color: #166534; }
    .status-partial { background: #fef3c7; color: #92400e; }
    .info-box { background: #fdf2f3; border: 1px solid #fce7e9; border-radius: 8px; padding: 16px; margin: 16px 0; }
    .info-box .row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #fce7e9; }
    .info-box .row:last-child { border-bottom: none; }
    .info-box .label { color: #6b7280; font-size: 13px; }
    .info-box .value { color: #111827; font-weight: 600; text-align: right; }
    table.items { width: 100%; border-collapse: collapse; margin: 16px 0; }
    table.items th { background: #fdf2f3; color: #6b7280; font-size: 12px; text-transform: uppercase; text-align: left; padding: 8px; }
    table.items td { padding: 8px; border-bottom: 1px solid #f3f4f6; font-size: 14px; color: #374151; }
    .footer { padding: 16px 24px; text-align: center; color: #9ca3af; font-size: 12px; background: #f9fafb; }
    .note { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px; color: #92400e; font-size: 13px; margin: 16px 0; }
</style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="header">
            <h1>Konfirmasi Pengambilan Seragam</h1>
        </div>
        <div class="body">
            <h2>Halo, {{ $student->name }}! 👋</h2>

            <p>
                Status pengambilan Anda:
                @if($transaction->status === 'completed')
                    <span class="status-badge status-completed">Lengkap</span>
                @else
                    <span class="status-badge status-partial">Sebagian</span>
                @endif
            </p>

            <div class="info-box">
                <div class="row"><span class="label">Jadwal</span><span class="value">{{ $schedule->name }}</span></div>
                <div class="row"><span class="label">Tanggal</span><span class="value">{{ \Carbon\Carbon::parse($transaction->pickup_time)->format('d M Y H:i') }}</span></div>
                @if($schedule->location)
                <div class="row"><span class="label">Lokasi</span><span class="value">{{ $schedule->location }}</span></div>
                @endif
            </div>

            <h3 style="color:#111827; font-size:16px; margin:16px 0 8px;">Detail Barang yang Diambil:</h3>
            <table class="items">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Ukuran</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->item?->name ?? 'Barang' }}</td>
                        <td>{{ $item->actual_size ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">Tidak ada item tercatat.</td></tr>
                @endforelse
                </tbody>
            </table>

            @if($transaction->status === 'partial')
                <div class="note">
                    <strong>Info:</strong> Sebagian barang belum bisa diambil karena stok tidak tersedia. Silakan hubungi petugas untuk pengambilan barang yang tertunda.
                </div>
            @endif

            @if($transaction->notes)
                <div class="note">
                    <strong>Catatan:</strong> {{ $transaction->notes }}
                </div>
            @endif
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Horizon') }} — Sistem Penerimaan Barang Mahasiswa
        </div>
    </div>
</div>
</body>
</html>