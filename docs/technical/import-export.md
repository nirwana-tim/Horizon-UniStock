# Import & Export Excel

## Pendahuluan

Fitur import/export adalah tulang punggung Admin (sebelumnya Finance). Admin menerima data dari kampus (mahasiswa, DP lunas) dan dari gudang (katalog, stok) dalam format Excel. Sistem harus bisa membaca data tersebut (import) dan menyajikan laporan (export) dengan format yang rapi, konsisten, dan mudah dibaca manusia.

**Alur Umum:**

```
Import: Download Template → Isi Data → Upload → Preview → Konfirmasi → Queue → Log
Export: Pilih Filter → Generate → Download (styled .xlsx)
```

---

## 1. Template Import (Admin Upload)

Template adalah file Excel kosong yang didownload Admin, diisi, lalu diupload kembali. Setiap template memiliki **2 sheet**:

| Sheet | Fungsi |
|-------|--------|
| **Petunjuk** | Panduan pengisian, kode referensi (prodi, ukuran), contoh baris |
| **Data** | Form isian dengan header rapi, validasi dropdown, freeze pane |

### Styling Template

| Elemen | Format |
|--------|--------|
| Header baris | Bold, background `#980416`, font putih (#FFFFFF), border semua sisi |
| Baris data | Font #333333, border bottom #CCCCCC tipis |
| Baris contoh (Petunjuk) | Font italic abu-abu #999999, background #F5F5F5 |
| Kolom required | Header dengan tanda `*` merah |
| Freeze pane | Baris header di-freeze |
| Auto filter | Diaktifkan di header |
| Validasi data | Dropdown untuk pilihan terbatas (prodi, gender, ukuran) |

### 1.1 Template Import Mahasiswa

**File:** `storage/app/templates/import_mahasiswa.xlsx`
**Class Generate:** `App\Exports\Templates\MahasiswaTemplateExport`
**Tujuan:** Import data mahasiswa baru (freshman) dan lanjutan (continuing) dari kampus.

**Sumber Data Excel:**
- `Student Data Template` — CAMPUS, COLLEGE, COURSE, YEAR, SEMESTER, STUDENT_ID
- `MASTER DATA FRESHMAN` — NIM, NAMA, PRODI, SERAGAM, SEPATU, Jenis Kelamin, Email

**Struktur Kolom (Sheet Data):**

| Kolom | Tipe | Required | Validasi | Contoh |
|-------|------|----------|----------|--------|
| NIM* | String (20) | Ya | unique, numeric 16 digit | 4112714401250002 |
| Nama Lengkap* | String (255) | Ya | - | NABILA LUTHFIYYAH SETIAWAN |
| Prodi* | String | Ya | Dropdown dari study_programs | D3 KEPERAWATAN 1 |
| Jenis Kelamin* | String | Ya | Dropdown: Laki-laki / Perempuan | Perempuan |
| Ukuran Baju* | String | Ya | Dropdown: S / M / L / XL / 2XL / 3XL / 4XL / 5XL | M |
| Ukuran Sepatu* | String | Ya | Dropdown: 36 / 37 / 38 / 39 / 40 / 41 / 42 / 43 / 44 / 45 | 38 |
| Email Kampus | String | No | Format email | nabila@krw.horizon.ac.id |
| Email Pribadi | String | No | Format email | nabila@gmail.com |
| Tipe* | String | Ya | Dropdown: Freshman / Continuing | Freshman |

**Contoh Tampilan:**

```
| NIM*             | Nama Lengkap*               | Prodi*           | Jenis Kelamin* | Ukuran Baju* | Ukuran Sepatu* | Tipe*       |
|------------------|------------------------------|------------------|----------------|--------------|----------------|-------------|
| 4112714401250002 | NABILA LUTHFIYYAH SETIAWAN   | D3 KEPERAWATAN 1 | Perempuan      | M            | 38             | Freshman    |
| 4112715401240002 | BUNGA CITRA ANDINI           | D3 KEBIDANAN 2   | Perempuan      | M            | 36             | Continuing  |
| 4112714401250003 | RIZVAL                       | D3 KEPERAWATAN 1 | Laki-laki      | L            | 42             | Freshman    |
```

### 1.2 Template Import DP Lunas

**File:** `storage/app/templates/import_dp_lunas.xlsx`
**Class Generate:** `App\Exports\Templates\DpLunasTemplateExport`
**Tujuan:** Import data mahasiswa yang sudah membayar DP (Down Payment) dari sistem keuangan kampus.

**Sumber Data Excel:** `ALL DATA DP PAID` — Student ID, Student Name, Course, Semester, Learning Modality

**Struktur Kolom:**

| Kolom | Tipe | Required | Validasi | Contoh |
|-------|------|----------|----------|--------|
| NIM* | String (20) | Ya | Harus ada di tabel students | 4112714201240001 |
| Nama Mahasiswa* | String | Ya | - | WULAN SARI NURFIANI |
| Prodi* | String | Ya | Dropdown dari study_programs | S1 KEPERAWATAN |
| Semester* | String | Ya | Dropdown: Year 1 Sem 1 / Year 1 Sem 2 / ... | Year 2 Sem 2 |
| Status Bayar* | String | Ya | Dropdown: Lunas / Belum Lunas | Lunas |
| Tanggal Bayar | Date | No | Format dd/mm/yyyy | 01/07/2025 |
| Nominal (Rp) | Number | No | Format #,##0 | 5000000 |

### 1.3 Template Import Katalog Barang

**File:** `storage/app/templates/import_katalog.xlsx`
**Class Generate:** `App\Exports\Templates\KatalogTemplateExport`
**Tujuan:** Import daftar barang/uniform beserta kode, kategori, varian ukuran.

**Sumber Data Excel:** `ID` sheet (Dummy Inventory Management) — ID, Category, Gender, Item, Department, Size

**Struktur Kolom:**

| Kolom | Tipe | Required | Validasi | Contoh |
|-------|------|----------|----------|--------|
| Kode Barang | String (Auto) | Auto | Format: KATEGORI-GENDER-TIPE-VARIANT-SIZE | UNF-L-SCB-02-03 |
| Kategori* | String | Ya | Dropdown: UNF / SHO / KTM / KIT / MRC | UNF |
| Gender* | String | Ya | Dropdown: L / P / U | L |
| Nama Item* | String (255) | Ya | - | Uniform Scrub Laki-Laki STIKES |
| Departemen* | String | Ya | Dropdown dari prodi/fakultas | 02 (STIKES) |
| Ukuran* | String | Ya | Dropdown: S / M / L / XL / 2XL / ... / All Size | S |
| Satuan* | String | Ya | Dropdown: Pcs / Pasang / Set / Pack | Pcs |

**Kode Barang Otomatis:** Format `KATEGORI-GENDER-TIPE-VARIANT-SIZE`
- Kategori: UNF (Uniform), SHO (Shoes), KTM (Kartu), KIT (Kit), MRC (Merchandise)
- Gender: L (Laki-laki), P (Perempuan), U (Unisex)
- TIPE: CLG (College), SCB (Scrub), COM (Community), LAB (Lab), CLN (Clinical), ALM (Almamater)
- Dept: 2 digit kode departemen
- Ukuran: 2 digit (01=S, 02=M, dst)

### 1.4 Template Import Harga Barang

**File:** `storage/app/templates/import_harga.xlsx`
**Class Generate:** `App\Exports\Templates\HargaTemplateExport`
**Tujuan:** Import harga jual dan HPP per tahun akademik.

**Sumber Data Excel:** `Items` sheet — ID, Description, PRICE 22/23, PRICE 23/24, PRICE 24/25

**Struktur Kolom:**

| Kolom | Tipe | Required | Validasi | Contoh |
|-------|------|----------|----------|--------|
| Kode Barang* | String | Ya | Harus ada di tabel items | UNF-L-SCB-02-03 |
| Nama Barang | String (Auto) | Auto | Muncul otomatis | Uniform Scrub Laki-Laki STIKES, Size S |
| Tahun Akademik* | String | Ya | Dropdown: 22/23 / 23/24 / 24/25 / 25/26 | 24/25 |
| Harga Jual (Rp)* | Number | Ya | Min: 0, format Rp | 190000 |
| HPP (Rp)* | Number | Ya | Min: 0, format Rp | 150000 |

### 1.5 Template Import Hak Barang (Entitlement)

**File:** `storage/app/templates/import_hak_barang.xlsx`
**Class Generate:** `App\Exports\Templates\HakBarangTemplateExport`
**Tujuan:** Menentukan barang apa saja yang berhak diterima oleh setiap program studi.

**Sumber Data Excel:** `2526 Ganjil/Genap - List Item` — mapping Prodi Level ke jenis barang

**Struktur Kolom:**

| Kolom | Tipe | Required | Validasi | Contoh |
|-------|------|----------|----------|--------|
| Prodi Level* | String | Ya | Dropdown dari program_levels | D3 KEPERAWATAN 1 |
| Tipe* | String | Ya | Dropdown: Freshman / Continuing | Freshman |
| Almamater | Number | No | 0 / 1 | 1 |
| Seragam Kuliah | Number | No | 0 / 1 | 1 |
| Seragam Praktek | Number | No | 0 / 1 | 1 |
| Scrub Suit | Number | No | 0 / 1 | 0 |
| Jas Lab | Number | No | 0 / 1 | 0 |
| Seragam Komunitas | Number | No | 0 / 1 | 1 |
| Sepatu Kuliah | Number | No | 0 / 1 | 1 |
| Sepatu Praktek | Number | No | 0 / 1 | 0 |
| Lanyard & Holder | Number | No | 0 / 1 | 1 |
| Name Tag | Number | No | 0 / 1 | 1 |
| Nursing Kit | Number | No | 0 / 1 | 0 |
| Midwifery Kit | Number | No | 0 / 1 | 0 |

Kolom barang bersifat dinamis — hanya barang yang aktif di sistem yang tampil.

---

## 2. Download Template

Admin mendownload template kosong sebelum mengisi data.

**Route:**

```
GET /admin/templates/{type}/download
```

| Parameter type | Template |
|----------------|----------|
| `mahasiswa` | Import Mahasiswa |
| `dp_lunas` | Import DP Lunas |
| `katalog` | Import Katalog Barang |
| `harga` | Import Harga Barang |
| `hak_barang` | Import Hak Barang |

**Cara Kerja:**
1. Cek apakah file statis ada di `storage/app/templates/import_{type}.xlsx`
2. Jika ada → `Storage::download()`
3. Jika tidak → `Excel::download(new TemplateExportClass)`

**Seeding File Statis:**
```bash
php artisan db:seed --class=TemplateSeeder
```
File template disimpan di `resources/templates/` lalu dicopy ke `storage/app/templates/`.

---

## 3. Import Data (Upload + Proses)

**Route:**

```
POST /admin/import
```

**Alur:**

```
Upload File → Validasi → Preview 10 baris → Konfirmasi → Queue Import → Log ke import_batches
```

### 3.1 Import Flow Detail

```
Request: POST /admin/import
Body: { import_type: "mahasiswa", file: file.xlsx }

1. Validasi file (mimes:xlsx,csv, max:10MB)
2. Parse Excel → Collection
3. Validasi setiap baris (rules dari Import class)
4. Jika ada error → return error dengan baris mana yang salah
5. Jika OK → simpan ke DB via Import class
6. Catat log di tabel import_batches
   → status: processing → completed / failed
   → total_rows, success_rows, failed_rows, error_log
7. Return redirect dengan pesan sukses/gagal
```

### 3.2 Import Types

| Type | Import Class | Target Table |
|------|-------------|-------------|
| `mahasiswa` | `App\Imports\StudentImport` | `students`, `users`, `student_size_profiles`, `student_size_items` |
| `dp_lunas` | `App\Imports\EligibilityImport` | `eligibility_records` |
| `katalog` | `App\Imports\ItemImport` | `items`, `item_variants` |
| `harga` | `App\Imports\ItemPriceImport` | `item_prices` |
| `hak_barang` | `App\Imports\EntitlementImport` | `entitlements`, `entitlement_items` |

### 3.3 Error Handling

| Skenario | Penanganan |
|----------|-----------|
| Baris duplikat NIM | Skip baris, catat di error_log |
| Cell kosong di kolom required | Kembalikan error dengan nomor baris |
| Format tanggal salah | Default ke null, catat warning |
| Prodi tidak ditemukan | Skip, catat "Prodi X tidak ditemukan" |
| File >10MB | Tolak dengan pesan "File terlalu besar" |
| Ekstensi salah | Tolak dengan pesan "Format file harus .xlsx atau .csv" |

### 3.4 Implementasi Import

```php
// app/Http/Controllers/ImportController.php
public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'import_type' => ['required', 'in:mahasiswa,dp_lunas,katalog,harga,hak_barang'],
        'file' => ['required', 'file', 'mimes:xlsx,csv', 'max:10240'],
    ]);

    $file = $request->file('file');
    $filePath = $file->storeAs('imports', time() . '_' . $file->getClientOriginalName(), 'local');

    $batch = $this->importService->processImport(
        $validated['import_type'],
        storage_path("app/{$filePath}"),
        $request->user()->id
    );

    if ($batch->status === 'completed') {
        return redirect()->route('import.index')
            ->with('success', "Import berhasil. {$batch->success_rows}/{$batch->total_rows} baris.");
    }

    return redirect()->route('import.index')
        ->with('error', "Import gagal. {$batch->failed_rows} baris error. Cek log.");
}
```

---

## 4. Export Laporan (Admin Download)

Laporan dihasilkan sistem dengan styling profesional. Admin memilih filter lalu mendownload file .xlsx.

### Styling Export

Semua laporan menggunakan `BaseExport` yang menyediakan styling konsisten:

| Elemen | Format |
|--------|--------|
| Judul laporan | Bold 14pt, font #980416, merge cells, row height 30 |
| Periode filter | Text 10pt, warna #666666, row height 20 |
| Header tabel | Background #980416, font putih bold 11pt, border #980416 |
| Baris data ganjil | Background putih (#FFFFFF) |
| Baris data genap | Background #F9F0F0 (stripe) |
| Total / summary | Background #E8D5D5, font bold 11pt, border double top |
| Angka rupiah | Format `#,##0` (tanpa "Rp" agar bisa diolah Excel) |
| Quantity | Format `#,##0` |
| Tanggal | Format `dd/mm/yyyy` |
| Freeze pane | Baris judul + header di-freeze |
| Auto filter | Di header tabel |
| Column width | Auto-fit berdasarkan konten |

### 4.1 Laporan Stok Inventaris

**Class:** `App\Exports\Reports\StockReport`
**File:** `Laporan_Stok_Inventaris_{periode}.xlsx`
**Analogi Sheet:** `List Stock` (Dummy Inventory Management)

**Filter:**
- Kategori Barang (semua / UNF / SHO / KTM / KIT)
- Gender (semua / L / P / U)

**Kolom:**

| Kolom | Format | Keterangan |
|-------|--------|-----------|
| No | Number | Urutan |
| Kode Barang | String | UNF-L-SCB-02-03 |
| Nama Barang | String | Uniform Scrub Laki-Laki STIKES, Size S |
| Kategori | String | UNF |
| Gender | String | L |
| Ukuran | String | S |
| Stok Awal | Number #,##0 | Saldo awal periode |
| Stok Masuk | Number #,##0 | Total penerimaan |
| Stok Keluar | Number #,##0 | Total distribusi/penjualan |
| **Stok Akhir** | Number #,##0 (bold) | Saldo saat ini |
| Nilai Stok (Rp) | Number #,##0 | Stok Akhir × HPP terakhir |

**Styling Khusus:**
- Baris dengan stok ≤ 0 → font merah (#CC0000)
- Subtotal per kategori → background #E8D5D5

### 4.2 Laporan Stok Opname

**Class:** `App\Exports\Reports\StockOpnameReport`
**File:** `Laporan_Stok_Opname_{tanggal}.xlsx`
**Analogi Sheet:** `Stock Opname` (Dummy Inventory Management)

**Filter:**
- Periode opname (dropdown dari stock_opnames)

**Kolom:**

| Kolom | Format | Keterangan |
|-------|--------|-----------|
| No | Number | - |
| Tanggal | dd/mm/yyyy | Tanggal opname |
| Kode Barang | String | - |
| Nama Barang | String | - |
| Ukuran | String | - |
| Stok Sistem | Number #,##0 | Data dari stock_balances |
| Stok Fisik | Number #,##0 | Input fisik |
| **Selisih** | Number #,##0 (bold) | Fisik - Sistem |
| Keterangan | String | Catatan |

**Styling Khusus:**
- Selisih positif → font hijau (#006600)
- Selisih negatif → font merah (#CC0000)
- Total baris: jumlah stok sistem, fisik, selisih

### 4.3 Laporan Rekap Pembagian

**Class:** `App\Exports\Reports\DistributionReport`
**File:** `Laporan_Rekap_Pembagian_{periode}.xlsx`
**Analogi Sheet:** `Summary Pembagian` (Dummy Freshman Seragam)

**Filter:**
- Periode distribusi
- Prodi (semua / spesifik)

**Kolom:**

| Kolom | Format | Keterangan |
|-------|--------|-----------|
| No | Number | - |
| Prodi | String | D3 KEPERAWATAN 1 |
| Eligible | Number #,##0 | Mahasiswa eligible |
| Sudah Ambil | Number #,##0 | Sudah melakukan transaksi |
| Belum Ambil | Number #,##0 | Eligible - Sudah Ambil |
| S | Number | Jumlah ukuran S |
| M | Number | Jumlah ukuran M |
| L | Number | Jumlah ukuran L |
| XL | Number | Jumlah ukuran XL |
| 2XL | Number | Jumlah ukuran 2XL |
| 3XL | Number | Jumlah ukuran 3XL |
| 4XL | Number | Jumlah ukuran 4XL |
| 5XL | Number | Jumlah ukuran 5XL |

**Styling Khusus:**
- Belum Ambil → font merah jika > 0
- Total row di akhir

### 4.4 Laporan GPM / Laba Kotor

**Class:** `App\Exports\Reports\GpmReport`
**File:** `Laporan_GPM_{periode}.xlsx`
**Analogi Sheet:** `CEK GPM` (Dummy Inventory Management)

**Filter:**
- Periode (tahun akademik)
- Kategori Barang

**Kolom:**

| Kolom | Format | Keterangan |
|-------|--------|-----------|
| No | Number | - |
| Kode Barang | String | UNF-L-SCB |
| Nama Barang | String | Uniform Scrub Laki-Laki |
| Harga Jual (Rp) | Number #,##0 | Rata-rata harga jual |
| HPP (Rp) | Number #,##0 | Rata-rata HPP |
| Qty Terjual | Number #,##0 | Total unit terjual |
| **Revenue (Rp)** | Number #,##0 (bold) | Harga Jual × Qty |
| **Cost (Rp)** | Number #,##0 (bold) | HPP × Qty |
| **Laba Kotor (Rp)** | Number #,##0 (bold) | Revenue - Cost |
| **Margin (%)** | Number #,##0.00% (bold) | (Laba / Revenue) × 100% |

**Styling Khusus:**
- Margin < 10% → background merah (#FFE0E0)
- Margin 10-20% → background kuning (#FFF8E0)
- Margin > 20% → background hijau (#E0FFE0)
- Total keseluruhan di baris terakhir

### 4.5 Laporan Kartu Stok

**Class:** `App\Exports\Reports\StockCardReport`
**File:** `Laporan_Kartu_Stok_{kode_barang}_{periode}.xlsx`
**Analogi Sheet:** `Inventory Card` (Dummy Inventory Management)

**Filter:**
- Kode Barang (dropdown atau search)
- Rentang tanggal (start_date, end_date)

**Kolom:**

| Kolom | Format | Keterangan |
|-------|--------|-----------|
| No | Number | - |
| Tanggal | dd/mm/yyyy | Tanggal transaksi |
| Referensi | String | No referensi (PO, SO, DJ) |
| Deskripsi | String | Keterangan transaksi |
| Masuk (IN) | Number #,##0 | Penerimaan stok |
| Keluar (OUT) | Number #,##0 | Pengeluaran stok |
| HPP Satuan (Rp) | Number #,##0 | Harga pokok per unit |
| Total HPP (Rp) | Number #,##0 | QTY × HPP |
| **Saldo Akhir** | Number #,##0 (bold) | Running balance |

**Styling Khusus:**
- Setiap item dipisah dengan separator (border thicker antar item)
- Total Masuk, Total Keluar, Saldo Akhir di akhir

### 4.6 Laporan Rekap Susut/Loss Stok

**Class:** `App\Exports\Reports\LossReport`
**File:** `Laporan_Susut_Stok_{periode}.xlsx`
**Analogi Sheet:** `Loss STO` (Dummy Inventory Management)

**Filter:**
- Periode (bulan/tahun)
- Kategori Barang

**Kolom:**

| Kolom | Format | Keterangan |
|-------|--------|-----------|
| No | Number | - |
| Periode | String | November 2025 |
| Item / Kategori | String | Uniform Scrub Laki-Laki |
| QTY Susut (Loss) | Number #,##0 | Selisih negatif |
| Harga Satuan (Rp) | Number #,##0 | HPP |
| **Total Loss (Rp)** | Number #,##0 (bold merah) | QTY Loss × Harga |
| QTY Surplus (Gain) | Number #,##0 | Selisih positif |
| **Total Surplus (Rp)** | Number #,##0 (bold hijau) | QTY Surplus × Harga |
| **Net Loss (Rp)** | Number #,##0 (bold) | Total Loss - Total Surplus |

**Styling Khusus:**
- Total Loss → font merah #CC0000
- Total Surplus → font hijau #006600
- Net Loss negatif → merah; positif → hijau
- Ringkasan per bulan + grand total

---

## 5. Template Engine (Generate + Styling)

### 5.1 BaseExport

Semua class export mewarisi `BaseExport` yang menyediakan helper styling:

```php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

abstract class BaseExport
{
    protected string $primaryColor = '980416';
    protected string $stripeColor = 'F9F0F0';
    protected string $totalColor = 'E8D5D5';

    protected function applyHeaderStyle(Worksheet $sheet, int $row = 1, int $colCount = 10): void
    {
        $range = 'A' . $row . ':' . $this->columnLetter($colCount) . $row;
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $this->primaryColor],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $this->primaryColor]],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    protected function applyDataStyle(Worksheet $sheet, int $startRow, int $endRow, int $colCount): void
    {
        for ($i = $startRow; $i <= $endRow; $i++) {
            $range = 'A' . $i . ':' . $this->columnLetter($colCount) . $i;
            $bgColor = ($i % 2 === 0) ? $this->stripeColor : 'FFFFFF';
            $sheet->getStyle($range)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bgColor],
                ],
                'borders' => [
                    'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
                ],
            ]);
        }
    }

    protected function applyTotalStyle(Worksheet $sheet, int $row, int $colCount): void
    {
        $range = 'A' . $row . ':' . $this->columnLetter($colCount) . $row;
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $this->totalColor],
            ],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '980416']],
            ],
        ]);
    }

    protected function setColumnWidths(Worksheet $sheet, array $widths): void
    {
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
    }

    protected function setFormatRupiah(Worksheet $sheet, string $column, int $startRow, int $endRow): void
    {
        $range = $column . $startRow . ':' . $column . $endRow;
        $sheet->getStyle($range)->getNumberFormat()->setFormatCode('#,##0');
    }

    private function columnLetter(int $index): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
    }
}
```

### 5.2 Implementasi Export Class

```php
namespace App\Exports\Reports;

use App\Exports\BaseExport;
use App\Models\StockBalance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReport extends BaseExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles
{
    // ... implementasi spesifik
}
```

---

## 6. Implementasi Routes

```php
// routes/web.php

// Download template import
Route::get('/templates/{type}/download', [TemplateController::class, 'download'])
    ->name('templates.download');

// Import data
Route::prefix('import')->name('import.')->group(function () {
    Route::get('/', [ImportController::class, 'index'])->name('index');
    Route::post('/preview', [ImportController::class, 'preview'])->name('preview');
    Route::post('/', [ImportController::class, 'store'])->name('store');
});

// Export laporan
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
    Route::get('/stock-opname/{stockOpname}', [ReportController::class, 'stockOpname'])->name('stock-opname');
    Route::get('/distribution', [ReportController::class, 'distribution'])->name('distribution');
    Route::get('/gpm', [ReportController::class, 'gpm'])->name('gpm');
    Route::get('/stock-card', [ReportController::class, 'stockCard'])->name('stock-card');
    Route::get('/loss', [ReportController::class, 'loss'])->name('loss');
});
```

---

## 7. Struktur File

```
app/
  Exports/
    BaseExport.php                          # Base class styling
    Templates/
      MahasiswaTemplateExport.php           # Download template mahasiswa
      DpLunasTemplateExport.php            # Download template DP lunas
      KatalogTemplateExport.php             # Download template katalog barang
      HargaTemplateExport.php              # Download template harga barang
      HakBarangTemplateExport.php           # Download template hak barang
    Reports/
      StockReport.php                       # Laporan stok inventaris
      StockOpnameReport.php                # Laporan stok opname
      DistributionReport.php               # Laporan rekap pembagian
      GpmReport.php                        # Laporan GPM
      StockCardReport.php                  # Laporan kartu stok
      LossReport.php                       # Laporan susut stok
  Imports/
    StudentImport.php                       # Import mahasiswa
    EligibilityImport.php                  # Import DP lunas
    ItemImport.php                         # Import katalog barang
    ItemPriceImport.php                    # Import harga barang
    EntitlementImport.php                  # Import hak barang
  Http/Controllers/
    ImportController.php                   # Upload + preview import
    TemplateController.php                 # Download template
    ReportController.php                   # Download laporan
  Services/
    ImportService.php                      # Logika proses import

resources/
  templates/                                # File statis template (.xlsx)
    import_mahasiswa.xlsx
    import_dp_lunas.xlsx
    import_katalog.xlsx
    import_harga.xlsx
    import_hak_barang.xlsx

storage/
  app/
    templates/                              # Copy dari resources/templates (via seeder)
    imports/                                # File upload sementara
```

---

## 8. Catatan Keamanan

| Aspek | Ketentuan |
|-------|-----------|
| Validasi file | Ekstensi .xlsx / .csv, max 10MB |
| Role akses import | `super_admin` dan `admin` via middleware `role:super_admin,finance` |
| Role akses export | `super_admin` dan `admin` |
| Logging | Semua import tercatat di `import_batches` dengan user_id |
| Temporary file | File upload dihapus setelah 24 jam (schedule command) |
| Error log | Disimpan sebagai JSON di `import_batches.error_log` |

---

## 9. Ringkasan

| Fitur | File | Status |
|-------|------|--------|
| Template Import Mahasiswa | `Templates/MahasiswaTemplateExport.php` | ✅ |
| Template Import DP Lunas | `Templates/DpLunasTemplateExport.php` | ✅ |
| Template Import Katalog Barang | `Templates/KatalogTemplateExport.php` | ✅ |
| Template Import Harga Barang | `Templates/HargaTemplateExport.php` | ✅ |
| Template Import Hak Barang | `Templates/HakBarangTemplateExport.php` | ✅ |
| Import Mahasiswa | `Imports/StudentImport.php` | ✅ |
| Import DP Lunas | `Imports/EligibilityImport.php` | ✅ |
| Import Katalog Barang | `Imports/ItemImport.php` | ✅ |
| Import Harga Barang | `Imports/ItemPriceImport.php` | 🔧 Perlu dibuat |
| Import Hak Barang | `Imports/EntitlementImport.php` | 🔧 Perlu dibuat |
| Laporan Stok Inventaris | `Reports/StockReport.php` | 🔧 Perlu dibuat |
| Laporan Stok Opname | `Reports/StockOpnameReport.php` | 🔧 Perlu dibuat |
| Laporan Rekap Pembagian | `Reports/DistributionReport.php` | ✅ (update styling) |
| Laporan GPM | `Reports/GpmReport.php` | ✅ (update styling) |
| Laporan Kartu Stok | `Reports/StockCardReport.php` | 🔧 Perlu dibuat |
| Laporan Susut Stok | `Reports/LossReport.php` | 🔧 Perlu dibuat |
| Base Styling | `Exports/BaseExport.php` | 🔧 Perlu dibuat |
| Download Template | `Controllers/TemplateController.php` | 🔧 Perlu dibuat |

✅ = Sudah ada (perlu update styling)
🔧 = Perlu dibuat baru
