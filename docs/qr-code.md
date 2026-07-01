# QR Code Generator (f9webltd/simple-qrcode)

## Apa Itu?

Package untuk generate QR Code di Laravel. Fork dari `simplesoftwareio/simple-qrcode` v5 yang kompatibel dengan Laravel 13 (menggunakan bacon/bacon-qr-code v3).

## Fitur yg Terinstall

| Fitur | Untuk Apa |
|-------|-----------|
| Generate QR | Bikin QR Code dari teks / URL / data |
| Output SVG | QR dalam format vector (scalable) |
| Output PNG | QR dalam format gambar raster |
| Custom Size | Atur ukuran pixel QR (default 100-500) |
| Custom Color | Ubah warna foreground & background |
| Custom Format | Format data (email, phone, SMS, Geo, dll) |
| Merge Image | Gabung logo/gambar di tengah QR |
| Error Correction | Level koreksi error (L, M, Q, H) |
| Save to Storage | Simpan QR Code ke file (storage, public) |
| Output Inline | Langsung render di Blade (`{!! !!}`) |

## 1. Generate QR di Blade (SVG)

```blade
{{-- QR dari teks --}}
{!! QrCode::size(200)->generate('https://horizon-unistock.test') !!}

{{-- QR dari NIM mahasiswa --}}
{!! QrCode::size(250)->generate($student->student_id) !!}

{{-- QR dengan warna custom --}}
{!! QrCode::size(200)
    ->color(38, 38, 38)
    ->backgroundColor(255, 255, 255)
    ->generate($student->student_id)
!!}
```

> **Note:** `QrCode::generate()` return HTML/SVG — harus pake `{!! !!}` (raw), bukan `{{ }}` (escaped).

## 2. Generate QR Token (Identity)

```php
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// Generate token unik untuk mahasiswa (1x seumur hidup)
$student->update([
    'qr_token' => Str::uuid(),
    'qr_generated_at' => now(),
]);

// Generate SVG dari token
$qrSvg = QrCode::size(200)->generate($student->qr_token);
```

## 3. Generate dengan Logo di Tengah

```php
use SimpleSoftwareIO\QrCode\Facades\QrCode;

QrCode::format('png')
    ->size(400)
    ->merge('/storage/logos/unistock-logo.png', 0.3, true) // 30% ukuran QR
    ->errorCorrection('H') // H = highest error correction, perlu buat logo
    ->generate('Student-2024-001', storage_path("app/public/qrcodes/with-logo.png"));
```

## 4. Format Data Spesifik

```php
{!! QrCode::email('student@example.com', 'Subject', 'Body message') !!}

{!! QrCode::phone('08123456789') !!}

{!! QrCode::SMS('08123456789', 'Pesan') !!}

{!! QrCode::geo(-6.9175, 107.6191) !!}
```

## 5. Error Correction Level

```php
QrCode::errorCorrection('L') // 7% data bisa rusak -> ukuran paling kecil
QrCode::errorCorrection('M') // 15% (default)
QrCode::errorCorrection('Q') // 25%
QrCode::errorCorrection('H') // 30% -> ukuran paling besar, perlu buat logo
```

## 6. Contoh Implementasi: QR Identity Mahasiswa

**Di Service:**
```php
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

public function generateQrToken(Student $student): void
{
    if ($student->qr_token) {
        return; // QR sudah ada, tidak regenerate
    }

    $student->update([
        'qr_token' => Str::uuid()->toString(),
        'qr_generated_at' => now(),
    ]);
}

public function getQrSvg(Student $student): string
{
    return QrCode::size(200)->generate($student->qr_token);
}
```

**Di Blade:**
```blade
@if ($student->qr_token)
    {!! QrCode::size(200)->generate($student->qr_token) !!}
@else
    <form action="{{ route('students.generate-qr', $student) }}" method="POST">
        @csrf
        <button type="submit">Generate QR</button>
    </form>
@endif
```

## 7. Cache QR (Opsional)

Untuk QR yg sering dipakai, cache hasil generate-nya:

```php
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

$qrSvg = Cache::remember("qr_{$student->id}", 86400, function () use ($student) {
    return QrCode::size(200)->generate($student->qr_token);
});
```

## Sumber
- https://github.com/f9webltd/simple-qrcode
- https://github.com/SimpleSoftwareIO/simple-qrcode

## Analogi
QR Code itu seperti stiker barcode di box barang — setiap mahasiswa punya satu kode unik seumur hidup. Tinggal scan buat lihat identitasnya, tanpa perlu ngetik manual.
