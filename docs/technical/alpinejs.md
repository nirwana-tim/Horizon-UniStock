# Alpine.js — Interaktivitas Frontend Ringan

**Sumber resmi:** https://alpinejs.dev/  
**Versi terinstall:** `^3.4` (lihat `package.json`)

## Apa Itu Alpine.js?

Alpine.js adalah JavaScript framework ringan (~12 KB) untuk menambahkan interaktivitas ke halaman HTML langsung dari markup. Cocok untuk Laravel + Blade karena tidak perlu SPA — cukup "sprinkle" JS di komponen yang butuh interaksi.

## Instalasi & Setup di Project Ini

**File:** `resources/js/app.js`

```js
import './bootstrap';
import Alpine from 'alpinejs';
import 'html5-qrcode';

window.Alpine = Alpine;

Alpine.start();
```

Cara kerja:
1. `npm install alpinejs` (`package.json` sudah include)
2. Import `alpinejs` dan daftarkan ke `window.Alpine`
3. Panggil `Alpine.start()` — cukup sekali per halaman
4. File ini di-load via `@vite('resources/js/app.js')` di layout

## Directive yang Digunakan di Project Ini

### `x-data` — Deklarasi komponen

Komponen paling dasar. Semua directive lain harus berada di dalam elemen yang punya `x-data`.

```blade
{{-- sidebar: state sidebar collapse + menu dropdown --}}
<aside x-data="{
    collapsed: false,
    masterOpen: {{ $masterOpen }},
    distributionOpen: {{ $distributionOpen }}
}">
```

```blade
{{-- dropdown: state buka/tutup --}}
<div x-data="{ open: false }">
```

```blade
{{-- searchable-select: state kompleks dengan computed property --}}
<div x-data="{
    open: false,
    search: '',
    selectedValue: '',
    selectedLabel: '',
    get filteredOptions() {
        return items.filter(i => i.label.toLowerCase().includes(this.search));
    }
}">
```

### `x-show` — Tampil/sembunyi

```blade
{{-- sidebar: sembunyiin label saat collapsed --}}
<span x-show="!collapsed" class="truncate">Dashboard</span>
```

```blade
{{-- sidebar: toggle menu dropdown --}}
<div x-show="masterOpen && !collapsed" x-collapse>
```

```blade
{{-- modal --}}
<div x-show="open" x-cloak>
```

```blade
{{-- alert dismiss --}}
<div x-data="{ show: true }" x-show="show" class="alert">
```

**Catatan:** `x-cloak` mencegah flash sebelum Alpine selesai load. Style `[x-cloak] { display: none }` sudah di `app.css`.

### `x-on` / `@` — Event listener

```blade
{{-- click outside untuk tutup dropdown --}}
<div x-data="{ open: false }" @click.outside="open = false">
```

```blade
{{-- modal: open/close via event --}}
<div x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
     x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
     x-on:keydown.escape.window="show = false">
```

```blade
{{-- sidebar collapse button --}}
<button @click="collapsed = true" title="Collapse sidebar">
```

**Event modifier:**
| Modifier | Fungsi | Contoh |
|----------|--------|--------|
| `.outside` | Klik di luar elemen | `@click.outside` |
| `.window` | Event di window global | `@keydown.escape.window` |
| `.prevent` | `e.preventDefault()` | `@keydown.tab.prevent` |
| `.debounce` | Delay eksekusi | `@input.debounce.300ms` |
| `.stop` | `e.stopPropagation()` | `@click.stop` |

### `x-model` — Two-way data binding

```blade
{{-- searchable-select: input pencarian --}}
<input x-model="search" @keydown="handleKeydown($event)">
```

```blade
{{-- distribution schedule: cascade faculty → prodi --}}
<select id="faculty_id" name="faculty_id" x-model="facultyId"
        @change="prodiId = ''">
```

### `x-text` — Set text content

```blade
<span x-text="selectedLabel || '{{ addslashes($placeholder) }}'"
      :class="selectedLabel ? 'text-gray-900' : 'text-gray-400'">
```

```blade
{{-- stock receive: item label --}}
<td x-text="item.item_label"></td>
```

### `x-html` — Set innerHTML

Digunakan untuk render HTML partial dari server-side table di masa depan.

```blade
<div x-html="tableHtml"></div>
```

### `x-for` — Looping

```blade
{{-- searchable-select: daftar opsi --}}
<template x-for="(opt, index) in filteredOptions" :key="opt.value">
    <li @click="select(opt.value, opt.label)">
        <span x-text="opt.label"></span>
    </li>
</template>
```

**Catatan:** `x-for` WAJIB di `<template>`, bukan di elemen langsung.

### `x-bind` / `:` — Bind attribute

```blade
{{-- class binding --}}
<div :class="collapsed ? 'w-16' : 'w-64'">
```

```blade
{{-- conditional class --}}
<li :class="{
    'bg-primary-50 text-primary-700': highlightedIndex === index,
    'hover:bg-gray-50 text-gray-900': highlightedIndex !== index
}">
```

```blade
{{-- value binding hidden input --}}
<input type="hidden" x-ref="hiddenInput" :value="selectedValue">
```

### `x-ref` — Referensi DOM

```blade
{{-- searchable-select: referensi ke input hidden + search --}}
<input type="hidden" x-ref="hiddenInput">
<input x-ref="searchInput" x-model="search">
```

### `x-init` — Inisialisasi

```blade
{{-- sidebar: tambah class transisi setelah mount --}}
<aside x-init="setTimeout(() => $el.classList.add('sidebar-transition'), 50)">
```

```blade
{{-- searchable-select: set label awal dari selectedValue --}}
<div x-init="init()">
```

### `x-cloak` — Cegah flash

```blade
<div x-show="open" x-cloak>
```

CSS sudah di `app.css`:
```css
[x-cloak] { display: none !important; }
```

### `x-collapse` — Animasi collapse/expand

Plugin bawaan Alpine. Dipakai di sidebar untuk menu dropdown.

```blade
<div x-show="masterOpen && !collapsed" x-collapse>
    {{-- submenu items --}}
</div>
```

## Magic Properties

| Magic | Fungsi | Contoh |
|-------|--------|--------|
| `$refs` | Akses elemen via `x-ref` | `$refs.searchInput.focus()` |
| `$watch` | Pantau perubahan data | `$watch('show', value => {...})` |
| `$nextTick` | Eksekusi setelah render | `$nextTick(() => $refs.searchInput.focus())` |
| `$dispatch` | Kirim custom event | `$dispatch('close-modal')` |
| `$el` | Elemen DOM saat ini | `$el.classList.add('...')` |

## Komponen Alpine.js di Project Ini

| Komponen | File | Fungsi |
|----------|------|--------|
| Sidebar | `components/sidebar.blade.php` | Collapse, menu accordion, user dropdown |
| Navbar | `layouts/navigation.blade.php` | Toggle menu mobile |
| Modal | `components/modal.blade.php` | Open/close via event, keyboard trap |
| Dropdown | `components/dropdown.blade.php` | Toggle + click outside |
| Delete Modal | `components/delete-modal.blade.php` | Konfirmasi hapus |
| Alert | `components/alert.blade.php` | Dismiss alert |
| Searchable Select | `components/searchable-select.blade.php` | Pencarian + pilih opsi |
| Stock Receive | `inventory/stock-receive/create.blade.php` | Dynamic item rows |
| Schedule | `distribution/distribution-schedule/create.blade.php` | Faculty → Prodi cascade |
| Staff Dashboard | `dashboards/staff.blade.php` | Toggle section |
| Login | `auth/login.blade.php` | Toggle show password |

## Best Practices

1. **Minimal logic di `x-data`** — Jika state kompleks, gunakan `Alpine.data()` atau service terpisah
2. **Gunakan `@` bukan `x-on:`** — Lebih pendek dan mudah dibaca
3. **Selalu pakai `x-cloak`** untuk elemen yang `x-show` — cegah flash
4. **Debounce input** — `@input.debounce.300ms` untuk search yang panggil API
5. **Jangan overload satu komponen** — Pecah `x-data` ke beberapa level jika terlalu kompleks
6. **`$nextTick()`** setelah ubah state yang perlu manipulasi DOM langsung

## Sumber

- Dokumentasi resmi Alpine.js: https://alpinejs.dev/
- Instalasi via NPM: https://alpinejs.dev/essentials/installation
- Referensi directive: https://alpinejs.dev/directives/data
- Tutorial memulai: https://alpinejs.dev/start-here
