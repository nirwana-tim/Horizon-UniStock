# Import & Export Excel

## Pendahuluan

Fitur import/export adalah tulang punggung Admin. Admin menerima data dari kampus (mahasiswa, DP lunas) dan dari gudang (katalog, stok) dalam format Excel. Sistem harus bisa membaca data tersebut (import) dan menyajikan laporan (export) dengan format yang rapi, konsisten, dan mudah dibaca manusia.

**Alur Umum:**

```
Import: Download Template → Isi Data → Upload → Preview → Commit → Log
Export: Pilih Filter → Generate → Download (styled .xlsx)
```

Import diproses **sinkron** (bukan queue) menggunakan Maatwebsite Excel `ToCollection`.

---

## 1. Template Import (Admin Upload)

Template adalah file Excel yang didownload Admin, diisi, lalu diupload kembali. Setiap template memiliki **1 sheet** bernama `Data` dengan struktur:

| Baris | Fungsi |
|-------|--------|
| 1 | Judul template (bold maroon `#980416`) |
| 2 | Subtitle (panduan singkat, kode referensi) |
| 3 | Contoh format (italic abu-abu) — khusus template yang pakai `WithCustomStartCell` |
| 4+ | Header kolom + area isian |

### Styling Template (`BaseExport`)

| Elemen | Format |
|--------|--------|
| Judul | Bold 14pt, font `#980416`, merge cells |
| Subtitle | Text 10pt, warna `#666666` |
| Header baris | Bold, background `#980416`, font putih (#FFFFFF), border semua sisi |
| Baris data | Font #333333, border bottom #CCCCCC tipis |
| Kolom required | Header dengan tanda `*` |
| Freeze pane | Baris header di-freeze |
| Auto filter | Diaktifkan di header |
| Angka rupiah | Format `#,##0` |

### 1.1 Template Import Mahasiswa

**Class Generate:** `App\Exports\Templates\MahasiswaTemplateExport`
**Type:** `mahasiswa`
**Tujuan:** Import data mahasiswa. Nilai `student_level` diisi otomatis berdasarkan mapping semester.

**Struktur Kolom:**

| Kolom | Tipe | Required | Keterangan |
|-------|------|----------|-----------|
| NIM * | String (20) | Ya | Unique, numeric |
| Nama Lengkap * | String (255) | Ya | - |
| Prodi * | String | Ya | Nama program studi |
| Jenis Kelamin * | String | Ya | Laki-laki / Perempuan |
| Ukuran Baju * | String | Ya | S / M / L / XL / dll |
| Ukuran Sepatu * | String | Ya | 36 / 37 / 38 / ... |
| Email Kampus | String | No | Format email |
| Email Pribadi | String | No | Format email |
| Tipe | String | No | Freshman / Continuing |

### 1.2 Template Import DP Lunas

**Class Generate:** `App\Exports\Templates\DpLunasTemplateExport`
**Type:** `dp_lunas`
**Tujuan:** Import data mahasiswa yang sudah membayar DP dari sistem keuangan kampus.

**Struktur Kolom:** `NIM *`, `Nama Mahasiswa *`, `Prodi *`, `Level *` (contoh `Y1S1`), `Status Bayar *` (Lunas / Belum Lunas).

### 1.3 Template Import Katalog Barang

**Class Generate:** `App\Exports\Templates\KatalogTemplateExport`
**Type:** `katalog`
**Tujuan:** Import master barang beserta qty stok per ukuran (varian).

**Struktur Kolom:** `Kategori *` (UNF / SHO / KTM / KIT / MRC), `Gender *` (L / P / U), `Nama Item *`, `Type *` (SCB / CLG / COM / LAB / CLN / ALM), `Departemen`, `Satuan *`, `Harga Jual (Rp)`, `HPP (Rp)`, lalu **kolom dinamis per ukuran** (dari `item_sizes`).

**Format Kode Barang (4 segmen):** `KATEGORI-GENDER-TIPE-VARIANT` — contoh `UNF-L-SCB-02`.
- Kategori: UNF (Uniform), SHO (Shoes), KTM (Kartu), KIT (Kit), MRC (Merchandise)
- Gender: L (Laki-laki), P (Perempuan), U (Unisex)
- TIPE: SCB (Scrub), CLG (College), COM (Community), LAB (Lab), CLN (Clinical), ALM (Almamater)
- VARIANT: 2 digit urutan (contoh `02`)

> Kode dihasilkan otomatis oleh `ItemService` / `ItemImport::generateBaseCode()` berdasarkan kategori/gender/tipe/departemen.

### 1.4 Template Import Harga Barang

**Class Generate:** `App\Exports\Templates\HargaTemplateExport`
**Type:** `harga`
**Tujuan:** Import harga jual dan HPP per tahun akademik.

**Struktur Kolom:** `Kode Barang *` (4 segmen, contoh `UNF-L-SCB-02`), `Nama Barang` (otomatis), `Tahun Akademik *` (22/23 / 23/24 / 24/25 / 25/26), `Harga Jual (Rp) *`, `HPP (Rp) *`.

### 1.5 Template Import Hak Barang (Entitlement)

**Class Generate:** `App\Exports\Templates\HakBarangTemplateExport`
**Type:** `hak_barang`
**Tujuan:** Menentukan barang berhak per Prodi Level (kode entitlement).

**Struktur Kolom:** `Prodi Level *`, `Tipe *` (Freshman / Continuing), lalu **kolom dinamis per item aktif** (qty barang berhak, 0 jika tidak).

### 1.6 Template Import Penerimaan Barang (Stock Receive)

**Class Generate:** `App\Exports\Templates\StockReceiveTemplateExport`
**Type:** `penerimaan`
**Tujuan:** Import barang masuk dari vendor.

**Struktur Kolom:** `Kode Barang *` (4 segmen, `UNF-L-SCB-02`), `SKU Varian` (`UNF-L-SCB-02-03`; kosongkan jika all size), `QTY *`, `Harga Satuan (Rp)`, `HPP (Rp)`, `Nama Vendor *`, `Tanggal Terima *` (YYYY-MM-DD), `Nomor Ref`, `Keterangan`.

### 1.7 Template Import Stock Opname

**Class Generate:** `App\Exports\Templates\StockOpnameTemplateExport`
**Type:** `stock_opname`
**Tujuan:** Import hasil hitung fisik bulanan.

**Struktur Kolom:** `Kode Barang *`, `Varian Ukuran *` (S/M/L/XL atau All Size), `Quantity Fisik *`.

---

## 2. Download Template

**Route:**

```
GET /templates/{type}/download          # name: templates.download (role: super_admin|admin)
```

| Parameter type | Template |
|----------------|----------|
| `mahasiswa` | Import Mahasiswa |
| `dp_lunas` | Import DP Lunas |
| `katalog` | Import Katalog Barang |
| `harga` | Import Harga Barang |
| `hak_barang` | Import Hak Barang |
| `penerimaan` | Import Penerimaan Barang |
| `stock_opname` | Import Stock Opname |

**Cara Kerja:** `TemplateController::download()` memetakan `type` → class export, lalu `Excel::download(new TemplateExportClass, "Template_Import_{type}.xlsx")`. Type tidak dikenal → `abort(404)`. Tidak ada file statis/seeder — semua template di-generate dinamis.

---

## 3. Import Data (Upload + Proses)

**Route:**

```
GET  /import              # name: import.index   (daftar + log import_batches)
POST /import              # name: import.store    (commit)
POST /import/preview      # name: import.preview  (validasi & preview)
GET  /import/{importBatch} # name: import.result
```

Semua dilindungi `auth` + `password.changed` + `role:super_admin|admin`; `store` diberi `throttle:5,1`, `preview` diberi `throttle:10,1`.

**Alur:**

```
Upload File → Validasi (import_type + file) → Preview → Commit → Simpan log import_batches
```

**Validasi `import_type`:** `in:student,eligibility,item,stock_opname,item_price,entitlement,stock_receive`

### 3.1 Import Types

| Type | Import Class | Target Table |
|------|-------------|-------------|
| `student` | `App\Imports\StudentImport` | `students`, `users`, `student_size_profiles`, `student_size_items` |
| `eligibility` | `App\Imports\EligibilityImport` | `eligibility_records` |
| `item` | `App\Imports\ItemImport` | `items`, `item_variants` (code 4 segmen, sku = code-SIZE) |
| `item_price` | `App\Imports\ItemPriceImport` | `item_prices` |
| `entitlement` | `App\Imports\EntitlementImport` | `entitlements`, `entitlement_items` |
| `stock_receive` | `App\Imports\StockReceiveImport` | `stock_receives`, `stock_receive_items`, `stock_batches`, `stock_movements` |
| `stock_opname` | `App\Imports\StockOpnameImport` | `stock_opname_items` |

### 3.2 Import Flow Detail

```
POST /import (store)
Body: { import_type: "student", file: file.xlsx }

1. Validasi file (mimes:xlsx,csv, max:10MB)
2. Parse Excel → Collection (ToCollection + WithHeadingRow)
3. Proses setiap baris via Import class (sinkron)
4. Simpan log di tabel import_batches
   → status: processing → completed / failed
   → total_rows, success_rows, failed_rows, error_log (JSON)
5. Redirect dengan pesan sukses/gagal
```

### 3.3 Error Handling

| Skenario | Penanganan |
|----------|-----------|
| Baris duplikat NIM | Skip baris, catat di error_log |
| Cell kosong di kolom required | Catat error dengan nomor baris |
| Prodi / kategori / ukuran tidak ditemukan | Skip, catat pesan |
| File >10MB | Tolak dengan pesan "File terlalu besar" |
| Ekstensi salah | Tolak dengan pesan format harus .xlsx/.csv |

---

## 4. Export Laporan (Admin Download)

Laporan dihasilkan sistem dengan styling profesional via `BaseExport`.

### Styling Export (`BaseExport`)

| Elemen | Format |
|--------|--------|
| Judul laporan | Bold 14pt, font `#980416`, merge cells, row height 30 |
| Periode filter | Text 10pt, warna `#666666`, row height 20 |
| Header tabel | Background `#980416`, font putih bold 11pt, border `#980416` |
| Baris data ganjil | Background putih (#FFFFFF) |
| Baris data genap | Background `#F9F0F0` (stripe) |
| Total / summary | Background `#E8D5D5`, font bold 11pt, border double top |
| Angka rupiah | Format `#,##0` (tanpa "Rp" agar bisa diolah Excel) |
| Quantity | Format `#,##0` |
| Tanggal | Format `dd/mm/yyyy` |
| Freeze pane | Baris judul + header di-freeze |
| Auto filter | Di header tabel |
| Column width | Auto-fit berdasarkan konten |

### Route Export (semua `role:super_admin|admin`, prefix `/report` name `report.*`)

Laporan diakses lewat halaman `GET /report` (`report.index`) dengan tombol download per laporan.

| Laporan | Class Export | Route Name |
|---------|--------------|-----------|
| Rekap Distribusi | `App\Exports\DistributionReportExport` | `report.distribution` |
| Stok Inventaris | `App\Exports\Reports\StockReport` | `report.stock` |
| Stok Opname | `App\Exports\Reports\StockOpnameReport` | `report.stock-opname` |
| Kartu Stok | `App\Exports\Reports\StockCardReport` | `report.stock-card` |
| GPM | `App\Exports\GpmReportExport` | `report.gpm` |
| GPM Cost | (via `Finance\GpmController`) | `report.gpm-cost` |
| Loss / Susut | `App\Exports\Reports\LossReport` | `report.loss` |
| Rekap Ukuran | `App\Exports\Reports\SizeRecapReport` | `report.size-recap` |
| Inventory | `App\Exports\InventoryReportExport` | `report.inventory` |
| Rekap Distribusi per Period | `App\Exports\DistributionReportExport` | `report.distribution-recap` |

> Struktur/kolom detail laporan mengikuti business logic di masing-masing class (umumnya: No, Kode Barang, Nama Barang, Kategori, Gender, Ukuran, Qty, Harga, Total, Saldo). Kode barang pada laporan memakai **4 segmen** (contoh `UNF-L-SCB-02`).

---

## 5. Template Engine (Generate + Styling)

### 5.1 BaseExport

Semua class export mewarisi `BaseExport` yang menyediakan helper styling (maroon `#980416`, stripe `#F9F0F0`, total `#E8D5D5`):

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

    protected function applyHeaderStyle(Worksheet $sheet, int $row = 1, int $colCount = 10): void;
    protected function applyDataStyle(Worksheet $sheet, int $startRow, int $endRow, int $colCount): void;
    protected function applyTotalStyle(Worksheet $sheet, int $row, int $colCount): void;
    protected function setTitle(Worksheet $sheet, string $title, int $colCount): void;
    protected function setSubtitle(Worksheet $sheet, string $subtitle, int $colCount): void;
    protected function setColumnWidths(Worksheet $sheet, array $widths): void;
    protected function setFormatRupiah(Worksheet $sheet, string $column, int $startRow, int $endRow): void;
    protected function setFormatNumber(Worksheet $sheet, string $column, int $startRow, int $endRow): void;
    protected function headerRow(): int;
    protected function dataStartRow(): int;
}
```

### 5.2 Implementasi Export Class

```php
namespace App\Exports\Reports;

use App\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
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
// routes/web.php (aktual)

// Download template import
Route::get('/templates/{type}/download', [TemplateController::class, 'download'])
    ->middleware(['auth', 'password.changed', 'role:super_admin|admin'])
    ->name('templates.download');

// Import data
Route::middleware(['auth', 'password.changed', 'role:super_admin|admin'])->prefix('import')->name('import.')->group(function () {
    Route::get('/', [ImportController::class, 'index'])->name('index');
    Route::get('/{importBatch}', [ImportController::class, 'result'])->name('result');
    Route::post('/', [ImportController::class, 'store'])->middleware('throttle:5,1')->name('store');
    Route::post('/preview', [ImportController::class, 'preview'])->middleware('throttle:10,1')->name('preview');
});

// Export laporan (prefix report, bukan reports)
Route::middleware(['auth', 'password.changed', 'role:super_admin|admin', 'throttle:10,1'])->prefix('report')->name('report.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('distribution', [ReportController::class, 'distribution'])->name('distribution');
    Route::get('distribution-recap', [ReportController::class, 'distributionRecap'])->name('distribution-recap');
    Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
    Route::get('gpm', [ReportController::class, 'gpm'])->name('gpm');
    Route::get('stock', [ReportController::class, 'stock'])->name('stock');
    Route::get('stock-opname', [ReportController::class, 'stockOpname'])->name('stock-opname');
    Route::get('stock-card', [ReportController::class, 'stockCard'])->name('stock-card');
    Route::get('loss', [ReportController::class, 'loss'])->name('loss');
    Route::get('gpm-cost', [GpmController::class, 'index'])->name('gpm-cost');
    Route::get('size-recap', [ReportController::class, 'sizeRecap'])->name('size-recap');
});
```

---

## 7. Struktur File

```
app/
  Exports/
    BaseExport.php                          # Base class styling
    Templates/
      MahasiswaTemplateExport.php           # Template mahasiswa
      DpLunasTemplateExport.php             # Template DP lunas
      KatalogTemplateExport.php             # Template katalog barang
      HargaTemplateExport.php               # Template harga barang
      HakBarangTemplateExport.php           # Template hak barang
      StockReceiveTemplateExport.php        # Template penerimaan barang
      StockOpnameTemplateExport.php         # Template stock opname
    Reports/
      StockReport.php                       # Laporan stok inventaris
      StockOpnameReport.php                 # Laporan stok opname
      StockCardReport.php                   # Laporan kartu stok
      LossReport.php                        # Laporan susut stok
      SizeRecapReport.php                   # Laporan rekap ukuran
    DistributionReportExport.php            # Laporan rekap distribusi
    GpmReportExport.php                     # Laporan GPM
    InventoryReportExport.php               # Laporan inventory
    StudentExport.php                       # Export data mahasiswa
    CredentialsExport.php                   # Export kredensial akun
  Imports/
    StudentImport.php                       # Import mahasiswa
    EligibilityImport.php                   # Import DP lunas
    ItemImport.php                          # Import katalog barang
    ItemPriceImport.php                     # Import harga barang
    EntitlementImport.php                   # Import hak barang
    StockReceiveImport.php                  # Import penerimaan barang
    StockOpnameImport.php                   # Import stock opname
  Http/Controllers/
    ImportController.php                    # Upload + preview + commit import
    TemplateController.php                  # Download template
    ReportController.php                    # Halaman & download laporan
  Services/
    ImportService.php                       # Logika proses import
```

---

## 8. Catatan Keamanan

| Aspek | Ketentuan |
|-------|-----------|
| Validasi file | Ekstensi .xlsx / .csv, max 10MB |
| Role akses import | `super_admin` dan `admin` via middleware `role:super_admin|admin` |
| Role akses export | `super_admin` dan `admin` |
| Logging | Semua import tercatat di `import_batches` dengan `imported_by` |
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
| Template Import Penerimaan | `Templates/StockReceiveTemplateExport.php` | ✅ |
| Template Import Stock Opname | `Templates/StockOpnameTemplateExport.php` | ✅ |
| Import Mahasiswa | `Imports/StudentImport.php` | ✅ |
| Import DP Lunas | `Imports/EligibilityImport.php` | ✅ |
| Import Katalog Barang | `Imports/ItemImport.php` | ✅ |
| Import Harga Barang | `Imports/ItemPriceImport.php` | ✅ |
| Import Hak Barang | `Imports/EntitlementImport.php` | ✅ |
| Import Penerimaan | `Imports/StockReceiveImport.php` | ✅ |
| Import Stock Opname | `Imports/StockOpnameImport.php` | ✅ |
| Laporan Stok Inventaris | `Reports/StockReport.php` | ✅ |
| Laporan Stok Opname | `Reports/StockOpnameReport.php` | ✅ |
| Laporan Rekap Distribusi | `DistributionReportExport.php` | ✅ |
| Laporan GPM | `GpmReportExport.php` | ✅ |
| Laporan Kartu Stok | `Reports/StockCardReport.php` | ✅ |
| Laporan Susut Stok | `Reports/LossReport.php` | ✅ |
| Laporan Rekap Ukuran | `Reports/SizeRecapReport.php` | ✅ |
| Base Styling | `Exports/BaseExport.php` | ✅ |
| Download Template | `Controllers/TemplateController.php` | ✅ |

✅ = Sudah diimplementasikan.