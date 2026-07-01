# Item Code System

## Format

```
KATEGORI-GENDER-TIPE-NOMOR
```

## Komponen

| Komponen | Kode | Arti |
|----------|------|------|
| **KATEGORI** | UNF | Uniform |
| | SHO | Shoes |
| | KTM | Kartu Mahasiswa |
| | KIT | Kit (Nursing/Midwifery) |
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
| | NUR | Nursing Kit |
| | MID | Midwifery Kit |
| **NOMOR** | 01, 02, 03... | Variasi ukuran |

## Contoh

| Kode | Deskripsi |
|------|-----------|
| `UNF-L-SCB-02-03` | Uniform Scrub Laki-Laki STIK ukuran 03 |
| `UNF-P-SCB-02-05` | Uniform Scrub Perempuan STIKES ukuran 05 |
| `SHO-P-CLC-02-41` | Shoes Clinical Perempuan STIKES ukuran 41 |
| `UNF-U-ALM-01-03` | Uniform Almamater Unisex Horizon ukuran 03 |
| `KTM-U-KTM-01-01` | KTM Kartu Mahasiswa Unisex |
| `KIT-U-NUR` | Kit Nursing Unisex |
| `KIT-U-MID` | Kit Midwifery Unisex |
| `SHO-L-CLG-01-03` | Shoes College Laki-Laki Horizon ukuran 03 |

## Aturan Penulisan

1. Semua kode menggunakan **UPPERCASE**
2. Dipisahkan dengan **tanda hubung** (`-`)
3. Nomor ukuran **2 digit** (01, 02, ... 09, 10, dst)
4. Kode harus **konsisten** di seluruh sistem
5. Kode yang sudah dibuat tidak boleh diubah (immutable)
