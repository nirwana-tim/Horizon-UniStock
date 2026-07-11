# Vite — Build Tool & Dev Server

**Sumber resmi:** https://vite.dev/guide/ | https://laravel.com/docs/13.x/vite  
**Versi terinstall:** `^7.0` (Vite) + `^2.0` (laravel-vite-plugin) — lihat `package.json`

## Apa Itu Vite?

Vite adalah build tool modern untuk frontend. Dua fungsi utama:
1. **Dev server** — Hot Module Replacement (HMR). Saat Anda edit CSS/JS, browser langsung update tanpa reload.
2. **Build production** — Bundle + minify + versioning file untuk production.

## Konfigurasi

**File:** `vite.config.js` (root project)

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### Penjelasan Konfigurasi

| Opsi | Nilai | Fungsi |
|------|-------|--------|
| `input` | `app.css`, `app.js` | Entry point — file yang akan di-bundle |
| `refresh` | `true` | Auto refresh browser saat Blade/view berubah |

### Entry Point

Ada 2 entry point yang di-load:

| File | Isi |
|------|-----|
| `resources/css/app.css` | Tailwind CSS (`@tailwind` directives), custom utilities, font Inter |
| `resources/js/app.js` | Alpine.js, Axios, html5-qrcode, Bootstrap |

## Cara Kerja di Blade

**File:** `resources/views/layouts/app.blade.php`

```blade
<head>
    {{-- ... --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

### Mode Development (`npm run dev`)

`@vite()` inject:
- **Vite client** — WebSocket untuk HMR
- **CSS** — Langsung dari Vite dev server (hot update)
- **JS** — ES module asli, bukan bundle

### Mode Production (`npm run build`)

`@vite()` inject:
- **CSS** — File minified dari `public/build/assets/` dengan hash versi
- **JS** — File bundled + tree-shaken + minified

## Commands

### Development

```bash
npm run dev
```

Menjalankan Vite dev server (biasanya di `http://localhost:5173`). File diubah → browser otomatis update.

### Production Build

```bash
npm run build
```

Hasil build disimpan di `public/build/`:
```
public/build/
├── manifest.json        # Mapping file asli → file build
└── assets/
    ├── app-abc123.css   # CSS minified + versioned
    └── app-xyz789.js    # JS bundled + versioned
```

### Fullstack Dev

```bash
composer run dev
```

Menjalankan 4 proses paralel via `concurrently`:
| Proses | Command |
|--------|---------|
| Laravel server | `php artisan serve` |
| Queue listener | `php artisan queue:listen` |
| Log viewer | `php artisan pail` |
| Vite | `npm run dev` |

## Alur Development

```
Anda edit file (.blade.php, .css, .js)
        │
        ▼
Vite detect perubahan → HMR (hot replace CSS/JS)
        │
        ▼
Browser update langsung (tanpa reload penuh)
        │
        ▼
Refresh: true → Vite pantau resources/views/
        │
        ▼
Jika Blade berubah → browser full refresh
```

## Plugin Vite

| Plugin | Versi | File Konfigurasi | Fungsi |
|--------|-------|-----------------|--------|
| `laravel-vite-plugin` | `^2.0` | `vite.config.js` | Integrasi Laravel dengan Vite |
| `@tailwindcss/vite` | `^4.0` | — | (Terinstall tapi tidak aktif — Tailwind via PostCSS) |

**Catatan:** Project menggunakan PostCSS untuk Tailwind (via `@tailwindcss/forms` dan `tailwind.config.js`), bukan `@tailwindcss/vite`. Plugin Vite Tailwind mungkin tidak digunakan.

## Environment Variables

Variabel environment dengan prefix `VITE_` bisa diakses di JavaScript:

```env
VITE_APP_NAME=UniStock
```

```js
console.log(import.meta.env.VITE_APP_NAME);
```

## Asset di Blade

### Static Asset

```blade
<img src="{{ Vite::asset('resources/images/logo.png') }}">
```

Agar Vite memproses asset, daftarkan di glob import `app.js`:
```js
import.meta.glob(['../images/**', '../fonts/**']);
```

### Inline Asset (untuk PDF, email)

```blade
<style>
    {!! Vite::content('resources/css/app.css') !!}
</style>
```

## Troubleshooting

### HMR tidak jalan
Pastikan `npm run dev` berjalan di terminal terpisah.

### Build error
```bash
# Hapus cache Vite
rm -rf public/build/
# Build ulang
npm run build
```

### CSS/JS tidak muncul di production
```bash
php artisan optimize:clear
npm run build
```

## Sumber

- Dokumentasi resmi Vite: https://vite.dev/guide/
- Laravel + Vite: https://laravel.com/docs/13.x/vite
- Laravel Vite Plugin: https://github.com/laravel/vite-plugin
- Laravel Blade integration: `docs/technical/laravel-blade.md`
