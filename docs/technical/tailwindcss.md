# Tailwind CSS — Utility-First CSS Framework

**Sumber resmi:** https://tailwindcss.com/docs/  
**Versi terinstall:** `^3.1` (lihat `package.json`)

## Apa Itu Tailwind CSS?

Tailwind CSS adalah utility-first CSS framework. Alih-alih menulis custom CSS, Anda cukup menggunakan class utility seperti `text-sm`, `bg-white`, `p-4`, `rounded-lg` langsung di HTML. Setiap class mewakili satu properti CSS.

## Instalasi & Konfigurasi

### Plugin

**File:** `tailwind.config.js`

```js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50:  '#fdf2f3',
                    100: '#fce7e9',
                    200: '#f7c5ca',
                    300: '#f09da5',
                    400: '#e55a68',
                    500: '#d6192c',
                    600: '#b80e20',
                    700: '#980416',
                    800: '#7a0513',
                    900: '#5c040e',
                    950: '#2e0105',
                },
            },
        },
    },

    plugins: [forms],
};
```

### CSS Entry Point

**File:** `resources/css/app.css`

```css
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap");

@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
    html {
        font-family: "Inter", ui-sans-serif, system-ui, -apple-system, sans-serif;
        scroll-behavior: smooth;
    }

    /* Prevent Alpine.js flash */
    [x-cloak] {
        display: none !important;
    }
}
```

### Integrasi Vite

Tailwind di-load via `@vite` di layout:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

## Warna Brand

Warna utama aplikasi adalah **Maroon** (`primary-700 = #980416`).

### Level Warna Primary

| Level | Kode | Penggunaan |
|-------|------|------------|
| `primary-50` | `#fdf2f3` | Background hover tombol outline, badge |
| `primary-100` | `#fce7e9` | Background ringan |
| `primary-500` | `#d6192c` | Focus ring, border aktif |
| `primary-600` | `#b80e20` | Hover tombol primer |
| `primary-700` | `#980416` | **BRAND** — tombol primer, teks aktif |
| `primary-800` | `#7a0513` | Active/pressed tombol |

### Akses via Utility

```blade
<button class="bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2">
    Simpan
</button>

<a class="text-primary-700 hover:text-primary-800 font-medium">
    Edit
</a>

<span class="bg-primary-50 text-primary-700 px-2 py-0.5 rounded-full text-xs">
    Aktif
</span>
```

### Jangan Gunakan Indigo/Blue

Framework ini tidak pakai warna Indigo/Blue default Tailwind. Ganti dengan `primary-*` di semua komponen.

## Font

- **Font utama:** Inter (Google Fonts)
- **Weight:** 400 (regular), 500 (medium), 600 (semibold), 700 (bold)
- **Penggunaan:**
  - Body: `font-sans text-sm text-gray-900`
  - Heading: `font-semibold text-lg text-gray-900`
  - Label: `font-medium text-sm text-gray-700`

## Utility Paling Sering Dipakai

### Layout & Spacing

| Utility | Contoh | Fungsi |
|---------|--------|--------|
| Container | `max-w-4xl mx-auto` | Pusatkan konten |
| Card | `bg-white rounded-xl border border-gray-200 shadow-sm p-5` | Kartu standar |
| Flex | `flex items-center justify-between` | Baris horizontal |
| Grid | `grid grid-cols-1 md:grid-cols-2 gap-6` | Grid responsif |
| Padding | `px-4 py-2`, `p-6` | Padding internal |
| Margin | `mt-4`, `mb-6`, `space-y-4` | Jarak antar elemen |

### Tombol

**Primer:**
```blade
<button class="bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2 text-sm font-medium">
    Simpan
</button>
```

**Sekunder (outline):**
```blade
<button class="border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg px-4 py-2 text-sm font-medium">
    Batal
</button>
```

**Danger:**
```blade
<button class="bg-red-600 text-white hover:bg-red-700 rounded-lg px-4 py-2 text-sm font-medium">
    Hapus
</button>
```

### Badge

Gunakan komponen `<x-badge>` yang sudah ada:
```blade
<x-badge type="success"   label="Lengkap" />
<x-badge type="warning"   label="Sebagian" />
<x-badge type="danger"    label="Gagal" />
<x-badge type="info"      label="Info" />
<x-badge type="neutral"   label="Draft" />
<x-badge type="primary"   label="Aktif" />
```

### Form Elements

Form sudah di-style otomatis oleh `@tailwindcss/forms` plugin.

```blade
<input class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
              focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
```

### Tabel

```blade
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-sm text-gray-900">{{ $item->name }}</td>
            </tr>
        </tbody>
    </table>
</div>
```

## Responsive Design

Gunakan prefix `sm:`, `md:`, `lg:` untuk breakpoint standar Tailwind.

```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
```

Breakpoints:
| Prefix | Lebar Min |
|--------|-----------|
| `sm:` | 640px |
| `md:` | 768px |
| `lg:` | 1024px |
| `xl:` | 1280px |

## Dark Mode

Dark mode **tidak diaktifkan** di project ini. Semua desain menggunakan warna terang (light mode).

## Custom Utilities di Project Ini

Beberapa utility custom didefinisikan di `app.css`:

```css
.sidebar-transition {
    transition: width 250ms cubic-bezier(0.4, 0, 0.2, 1);
}

.custom-scroll::-webkit-scrollbar { width: 4px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #e5e2e2; border-radius: 2px; }

.sidebar-item-active {
    @apply bg-primary-50 text-primary-700 border-l-4 border-primary-700 font-medium;
}

.sidebar-item {
    @apply text-gray-600 border-l-4 border-transparent hover:bg-gray-50 hover:text-gray-800;
}
```

## Sumber

- Dokumentasi resmi Tailwind CSS: https://tailwindcss.com/docs/
- Instalasi via PostCSS: https://tailwindcss.com/docs/installation/using-postcss
- Utility lengkap: https://tailwindcss.com/docs/styling-with-utility-classes
- Custom colors: https://tailwindcss.com/docs/colors
- Plugin Forms: https://github.com/tailwindlabs/tailwindcss-forms
- Panduan desain project ini: `docs/guides/desain.md`
