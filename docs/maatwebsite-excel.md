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

## 2. Import Basic

**`app/Imports/StudentsImport.php`:**
```php
<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $user = User::create([
            'name' => $row['nama'],
            'email' => $row['email'],
            'password' => Hash::make($row['nim']), // password default = NIM
        ]);

        return new Student([
            'user_id' => $user->id,
            'student_id' => $row['nim'],
            'major_id' => $row['jurusan_id'],
            'batch_year' => $row['angkatan'],
        ]);
    }

    public function rules(): array
    {
        return [
            'nim' => 'required|unique:students,student_id',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'angkatan' => 'required|numeric',
        ];
    }
}
```

**Di controller:**
```php
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

public function import(Request $request)
{
    $request->validate(['file' => 'required|mimes:xlsx,csv']);

    Excel::import(new StudentsImport, $request->file('file'));

    return back()->with('success', 'Import berhasil');
}
```

## 3. Import Queue (File Besar)

```php
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

public function importLarge(Request $request)
{
    $import = new StudentsImport();
    Excel::queueImport($import, $request->file('file'));

    return back()->with('success', 'Import diproses di background');
}
```

Butuh queue worker: `php artisan queue:work`

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
