# Maatwebsite Laravel Excel

## Apa Itu?

Package untuk import & export file Excel (XLSX, XLS, CSV) di Laravel. Didukung oleh PhpSpreadsheet di belakangnya.

## Fitur yg Terinstall

| Fitur | Untuk Apa |
|-------|-----------|
| Export Collection | Export array/collection ke Excel |
| Export Query | Export langsung dari Eloquent query builder |
| With Headings | Baris header (kolom A, B, C...) |
| With Styles | Styling cell (bold, color, border) |
| With Mapping | Ubah format data sebelum export |
| Import To Model | Import Excel langsung ke database via model |
| Import To Array | Import Excel ke array aja (tanpa DB) |
| With Heading Row | Baris pertama sebagai nama kolom |
| With Validation | Validasi tiap baris pas import |
| Queue Import | Import file besar di background (queue job) |
| Multiple Sheets | Export/import multi sheet |
| Custom Cell Format | Format angka, tanggal, dll |
| Exportable | Download langsung (response) atau simpan ke storage |
| Importable | Upload file via form |

## 1. Export Basic

**Buat export class:**
```bash
php artisan make:export StudentsExport --model=App\\Models\\Student
```

**`app/Exports/StudentsExport.php`:**
```php
<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Student::with('user', 'major')->get();
    }

    public function headings(): array
    {
        return ['NIM', 'Nama', 'Jurusan', 'Angkatan', 'Status'];
    }

    public function map($student): array
    {
        return [
            $student->student_id,
            $student->user->name,
            $student->major->name,
            $student->batch_year,
            $student->is_active ? 'Aktif' : 'Tidak Aktif',
        ];
    }
}
```

**Di controller:**
```php
use App\Exports\StudentsExport;
use Maatwebsite\Excel\Facades\Excel;

public function export()
{
    return Excel::download(new StudentsExport, 'mahasiswa.xlsx');
}

public function exportAndSave()
{
    Excel::store(new StudentsExport, 'exports/mahasiswa.xlsx', 'public');
}
```

## 2. Import Basic (pola proyek: `ToCollection` + `WithHeadingRow`)

> Proyek UniStock memakai **`ToCollection` + `WithHeadingRow`** — bukan `ToModel`. Setiap import class membaca seluruh baris sebagai Collection, lalu `ImportService` yang memproses baris per baris (resolve relasi, validasi, insert) dan mencatat log.

**`app/Imports/ItemImport.php` (contoh):**
```php
<?php

namespace App\Imports;

use App\Models\ItemCategory;
use App\Models\ItemDepartment;
use App\Models\ItemType;
use App\Services\Master\ItemService;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ItemImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function __construct(private readonly ItemService $itemService) {}

    public function collection(Collection $rows): void
    {
        // resolve kategori/tipe/departemen sekali, generate base code 4 segmen,
        // lalu buat item + varian (sku = code-SIZE)
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
```

**Di controller (`ImportController::store`):** file diproses via `ImportService::processImport()` yang meng-parse file, memanggil import class, dan menulis `import_batches` (status processing → completed/failed).

## 4. Export dengan Styling

```php
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithStyles
{
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
                  'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => '4472C4']]],
        ];
    }
}
```

## 5. Konfigurasi (config/excel.php)

```php
'exports' => [
    'temp_path' => storage_path('app/temp'),
],
'imports' => [
    'read_only' => false,
    'heading_row' => [
        'formatter' => 'slug', // ubah "Nama Lengkap" jadi "nama_lengkap"
    ],
],
```

## Sumber
- https://docs.laravel-excel.com
- https://github.com/SpartnerNL/Laravel-Excel

## Analogi
Maatwebsite Excel itu seperti resepsionis pabrik — bisa nulis ribuan data ke kertas (export) dalam sekejap, dan bisa baca kertas isian (import) trus masukin ke database.
