# Risiko & Mitigasi

## Risiko Data

**Masalah:** Data Excel tidak konsisten

**Solusi:**
- Import validation
- Preview sebelum commit
- Error report per baris

## Risiko Hari-H

**Masalah:** QR gagal atau sistem lambat

**Solusi:**
- Manual search NIM (fallback)
- Backup Excel data mahasiswa
- Database backup sebelum hari-H
- Fallback procedure

## Risiko Scope

**Masalah:** Inventory terlalu besar untuk MVP

**Solusi:** Fokus Freshman Distribution dulu, Inventory lanjutan belakangan

---

## Manajemen Data & Import

### Import Excel Flow

```
Upload Excel
     ↓
Validasi Format & Data
     ↓
Preview Hasil (sebelum commit)
     ↓
Konfirmasi → Commit ke Database
     ↓
Simpan Import Log (siapa, kapan, hasil)
```

### Jenis Import

| Import | Target | Keterangan |
|--------|--------|-----------|
| Data Mahasiswa | `students` | NIM, nama, prodi, level, email |
| Eligible Payment | `eligibility_records` | Status bayar per mahasiswa |
| Item & Stock | `items`, `stock_receives` | Data barang & stok awal |
| Stock Opname | `stock_opnames` | Hasil opname fisik bulanan |
| Item Master + Harga | `items`, `item_variants` | Data barang dengan harga jual |

### Report MVP

#### Distribution Report
- Sudah ambil, belum ambil, partial
- Detail item per mahasiswa

#### Inventory Report
- Stock balance per item
- Barang keluar (distribution)
- Movement history (IN/OUT)
- Stock Opname report (variance per item)

#### GPM / Cost Report
- HPP per item per batch
- Harga jual per item
- Laba/Rugi per item = (Harga Jual - HPP) × Qty Terjual
- Laba/Rugi per kategori
- Laba/Rugi per periode

**Format Export:** Excel (.xlsx)

---

## Fallback Hari-H

Jika sistem bermasalah saat hari distribusi:

1. **Export data mahasiswa** dari sistem
2. **Manual search NIM** jika QR tidak bisa dipindai
3. **Catat transaksi sementara** di Excel backup
4. **Import kembali** setelah sistem normal

### Checklist Fallback

- [ ] Data mahasiswa ter-export (semua eligible)
- [ ] Template Excel siap untuk manual entry
- [ ] Database backup tersimpan
- [ ] Nomor kontak IT support aktif
