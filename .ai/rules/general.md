---
paths:
  - '**'
---

# General

## Deploy produksi wajib jalankan composer dump-autoload
Produksi (unistok.nirwana.biz.id, cPanel, tanpa SSH) di-deploy via `git pull` saja. `app/helpers.php` terdaftar di composer.json `autoload.files`; jika autoloader tidak di-refresh, fungsi global seperti `escapeLike()` hilang → 500 "Call to undefined function" di semua halaman yang memakai pencarian (trait EscapesLikeWildcards, EntitlementImport, StudentExport). Setiap deploy WAJIB: `git pull origin main && composer dump-autoload --optimize --no-interaction && php artisan optimize:clear`. Jangan pakai helper global `escapeLike()` di kode baru — pakai str_replace inline atau method trait.
