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

## 2. Generate & Simpan PNG

```php
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// Simpan ke public/storage
$qrPath = 'qrcodes/' . $student->student_id . '.png';
QrCode::format('png')
    ->size(300)
    ->generate($student->student_id, storage_path("app/public/{$qrPath}"));

// Simpan path ke database
$student->update(['qr_code_path' => $qrPath]);
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

**Di controller:**
```php
public function generateQr(Student $student)
{
    $qrPath = "qrcodes/{$student->student_id}.png";

    if (!Storage::disk('public')->exists($qrPath)) {
        QrCode::format('png')
            ->size(300)
            ->color(38, 38, 128)
            ->generate($student->student_id, storage_path("app/public/{$qrPath}"));

        $student->update(['qr_code_path' => $qrPath]);
    }

    return redirect()->back()->with('success', 'QR Code generated');
}
```

**Di Blade:**
```blade
@if ($student->qr_code_path)
    <img src="{{ Storage::url($student->qr_code_path) }}" alt="QR {{ $student->student_id }}" width="200">
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
    return QrCode::size(200)->generate($student->student_id);
});
```

## Sumber
- https://github.com/f9webltd/simple-qrcode
- https://github.com/SimpleSoftwareIO/simple-qrcode

## Analogi
QR Code itu seperti stiker barcode di box barang — setiap mahasiswa punya satu kode unik seumur hidup. Tinggal scan buat lihat identitasnya, tanpa perlu ngetik manual.
