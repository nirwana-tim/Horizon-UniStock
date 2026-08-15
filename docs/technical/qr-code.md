# QR Code Generator (bacon/bacon-qr-code)

## Apa Itu?

Package untuk generate QR Code di Laravel. Di proyek ini QR berisi **NIM mahasiswa** (identitas permanen, 1x seumur hidup) — bukan token random. QR di-render sebagai **PNG data URL** lalu ditampilkan di Blade `<img>`.

## Fitur yg Terinstall

| Fitur | Untuk Apa |
|-------|-----------|
| Generate QR | Bikin QR Code dari teks (NIM) |
| Output PNG | QR dalam format gambar raster (data URL base64) |
| Custom Size | Atur ukuran pixel QR |
| Error Correction | Level koreksi error (L, M, Q, H) |
| Data URL | Langsung dirender di Blade via `<img src="data:image/png;base64,...">` |

## 1. Service: `QrCodeService`

**`app/Services/QrCodeService.php`**

```php
<?php

namespace App\Services;

use App\Models\Student;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;

class QrCodeService
{
    public function getQrPngDataUrl(Student $student, int $size = 300): string
    {
        $renderer = new GDLibRenderer($size, 4, 'png');
        $writer = new Writer($renderer);
        $png = $writer->writeString($student->nim, Encoder::DEFAULT_BYTE_MODE_ENCODING, ErrorCorrectionLevel::H());

        return 'data:image/png;base64,' . base64_encode($png);
    }
}
```

> ⚠️ Membutuhkan ekstensi PHP **GD** (untuk `GDLibRenderer`).

## 2. Pemakaian di Controller / Service

Inject service via constructor (dependency injection):

```php
use App\Services\QrCodeService;

public function __construct(private readonly QrCodeService $qrCodeService) {}

public function qr(): View
{
    $student = auth()->user()->student;
    $qrDataUrl = $this->qrCodeService->getQrPngDataUrl($student, 300);

    return view('student.qr', compact('qrDataUrl'));
}
```

## 3. Tampilkan di Blade

```blade
{{-- QR berisi NIM mahasiswa --}}
<img src="{{ $qrDataUrl }}" alt="QR Identity {{ $student->nim }}" class="w-64 h-64">
```

## 4. Scan

QR di-scan via **HTML5 QR Scanner** (`html5-qrcode`) di halaman `distribution.scan`. Hasil scan adalah NIM → redirect ke `distribution.scan.student/{nim}`. Manual search NIM tetap tersedia sebagai fallback.

> Tidak ada kolom `qr_token` / `qr_generated_at` di tabel `students` (legacy dihapus). Identitas QR sepenuhnya dari NIM.

## Sumber
- https://github.com/Bacon/BaconQrCode
- https://github.com/mebjas/html5-qrcode

## Analogi
QR Code itu seperti stiker barcode di box barang — setiap mahasiswa punya satu identitas (NIM) seumur hidup. Tinggal scan buat lihat identitasnya, tanpa perlu ngetik manual.