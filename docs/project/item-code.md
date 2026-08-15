# Item Code System

## Format

```
KATEGORI-GENDER-TIPE-VARIANT
```

Setiap item memiliki **base code** yang disimpan di `items.code` dan `items.base_code` (nilainya identik, unique). Varian ukuran menyimpan **SKU** lengkap di `item_variants.sku`:

```
SKU = ITEM_CODE + "-" + SIZE
```

| Tingkat | Format | Contoh | Tabel |
|---------|--------|--------|-------|
| Item code / base code | `KATEGORI-GENDER-TIPE-VARIANT` | `UNF-U-ALM-10` | `items.code` = `items.base_code` |
| SKU varian | `KATEGORI-GENDER-TIPE-VARIANT-SIZE` | `UNF-U-ALM-10-03` | `item_variants.sku` |

## Komponen

| Komponen | Kode | Arti |
|----------|------|------|
| **KATEGORI** | UNF | Uniform |
| | SHO | Shoes |
| | KTM | Kartu Mahasiswa |
| | KIT | Kit (Nursing/Midwifery) |
| | MRC | Merchandise |
| **GENDER** | L | Laki-laki |
| | P | Perempuan |
| | U | Unisex |
| **TIPE** | SCB | Scrub Suit |
| | CLC | Clinical Uniform |
| | ALM | Almamater |
| | CLG | College Uniform |
| | COM | Community Uniform |
| | LAB | Laboratory Gown |
| | YDH | Lanyard & Holder |
| | KTM | Kartu Mahasiswa |
| | TAG | Name Tag / Nameplate |
| | NUR | Nursing Kit |
| | MID | Midwifery Kit |
| | TBR | Tumbler / Merchandise |
| **VARIANT** | 01, 02, 03... | Departemen (lihat tabel Variant) |
| **SIZE** | 03, 04, 05... | Kode ukuran (lihat tabel Size Mapping) |

### Variant

Kode variant merepresentasikan departemen (`item_departments`):

| Variant | Departemen |
|---------|-----------|
| 01 | Horizon (main campus) |
| 02 | STIKES |
| 03 | STMIK |
| 04 | STIE |
| 05 | S1 KEP |
| 06 | D3 KEP |
| 07 | D3 KEB |
| 08 | S1 KEP NR |
| 09 | NERS |
| 10 | S1 SI |
| 11 | S1 IF |
| 12 | Management |
| 13 | Akuntansi |
| 14 | Pariwisata |

Jika item tidak memiliki departemen, gunakan `00`.

### Size Mapping

Ukuran di database disimpan dalam dua kolom:
- `size` — kode numerik (untuk konsistensi kode)
- `size_label` — label yang ditampilkan ke pengguna

#### Uniform (UNF)

| Size | Label |
|------|-------|
| 01 | All Size |
| 02 | XS |
| 03 | S |
| 04 | M |
| 05 | L |
| 06 | XL |
| 07 | 2XL |
| 08 | 3XL |
| 09 | 4XL |
| 10 | 5XL |
| 11 | 6XL |
| 12-20 | 7XL - 15XL |

#### Shoes (SHO)

Size menggunakan ukuran sepatu asli (34, 35, 36, ... 46).

#### Accessories (KTM, Kit, dll)

Size `01` = `All Size` (satu ukuran).

## Contoh

| Kode | SKU | Deskripsi |
|------|-----|-----------|
| `UNF-L-SCB-02` | `UNF-L-SCB-02-03` | Uniform Scrub Laki-Laki STIKES ukuran S |
| `UNF-P-SCB-02` | `UNF-P-SCB-02-05` | Uniform Scrub Perempuan STIKES ukuran L |
| `SHO-P-CLC-02` | `SHO-P-CLC-02-41` | Shoes Clinical Perempuan STIKES ukuran 41 |
| `UNF-U-ALM-01` | `UNF-U-ALM-01-03` | Almamater Unisex Horizon ukuran S |
| `UNF-U-ALM-02` | `UNF-U-ALM-02-07` | Almamater Unisex STIKES ukuran 2XL |
| `KTM-U-KTM-01` | `KTM-U-KTM-01-01` | KTM Kartu Mahasiswa Unisex (All Size) |
| `KTM-U-YDH-01` | `KTM-U-YDH-01-01` | KTM Lanyard & Holder Unisex (All Size) |
| `KTM-U-TAG-02` | `KTM-U-TAG-02-01` | Name Tag Unisex STIKES (All Size) |
| `KIT-U-NUR-06` | `KIT-U-NUR-06-01` | Nursing Kit D3 Keperawatan (All Size) |
| `KIT-U-MID-02` | `KIT-U-MID-02-01` | Midwifery Kit STIKES (All Size) |
| `SHO-L-CLG-02` | `SHO-L-CLG-02-37` | Shoes College Laki-Laki STIKES ukuran 37 |

## Aturan Penulisan

1. Semua kode menggunakan **UPPERCASE**
2. Dipisahkan dengan **tanda hubung** (`-`)
3. Variant dan ukuran **2 digit** (01, 02, ... 09, 10, dst)
4. Kode harus **konsisten** di seluruh sistem
5. Kode yang sudah dibuat tidak boleh diubah (immutable)

## Pembuatan Kode

Kode dihasilkan otomatis dari relasi master data (`ItemService`):

```php
$code = $category->code . '-' . $gender . '-' . ($type?->code ?? 'XX') . '-' . ($department?->code ?? '00');
```

Alur yang sama berlaku saat import katalog (`ItemImport`) agar konsisten dengan input manual.