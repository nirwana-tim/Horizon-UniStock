# Panduan Desain — UniStock

> **Warna Primer:** `#980416` (Maroon) — Monochromatic Harmony
> **Framework CSS:** Tailwind CSS
> **Target:** Mobile-first untuk Staff & Student, Desktop-optimized untuk Admin & Super Admin

---

## Daftar Isi

1. [Filosofi Desain](#1-filosofi-desain)
2. [Color Palette](#2-color-palette)
3. [Tipografi](#3-tipografi)
4. [Spacing & Layout](#4-spacing--layout)
5. [Responsive Strategy](#5-responsive-strategy)
6. [Navigation](#6-navigation)
7. [Komponen](#7-komponen)
8. [Form & Validasi](#8-form--validasi)
9. [Tabel Data](#9-tabel-data)
10. [QR Scanner](#10-qr-scanner)
11. [Status & Feedback](#11-status--feedback)
12. [Halaman Contoh](#12-halaman-contoh)

---

## 1. Filosofi Desain

### Prinsip Utama

| Prinsip | Penjelasan |
|---------|-----------|
| **Monochromatic** | Satu hue (353°) dari `#980416` — tints, shades, tones menciptakan harmoni visual |
| **Role-Based Responsive** | Staff & Student prioritaskan mobile; Admin & Super Admin prioritaskan desktop |
| **Utility-First** | Semua styling menggunakan Tailwind utility classes, tanpa CSS custom |
| **Accessibility** | Kontras WCAG AA minimum, touch target ≥44px untuk mobile |
| **Consistency** | 1 warna primer, 1 font stack, 1 spacing scale di seluruh sistem |

### Target per Role

| Role | Primary Device | Approach |
|------|--------------|----------|
| **Student** | Mobile HP (320-428px) | Bottom nav, full-width card, QR besar, tap-friendly |
| **Staff** | Mobile HP (360-428px) | Scanner kamera fullscreen, checklist centang besar, tombol aksi dominan |
| **Admin** | Desktop/Laptop (1024-1920px) | Sidebar, tabel data, multi-step wizard, export |
| **Super Admin** | Desktop/Laptop (1280-1920px) | Sidebar, tabel CRUD, filter panel, audit log |

---

## 2. Color Palette

### Primary — Monochromatic Scale

Base hue: `hsl(353, 95%, 31%)` = `#980416`

```
Tints (putih)    ←     Base     →    Shades (hitam)
#fdf2f3  #fce7e9  #f7c5ca  #f09da5  #e55a68  #d6192c  #b80e20  #980416  #7a0513  #5c040e  #2e0105
  50       100      200      300      400      500      600      700      800      900      950
```

| Token | Hex | RGB | Penggunaan |
|-------|-----|-----|-----------|
| `primary-50` | `#fdf2f3` | `253, 242, 243` | Background halaman, surface halus |
| `primary-100` | `#fce7e9` | `252, 231, 233` | Background card, section |
| `primary-200` | `#f7c5ca` | `247, 197, 202` | Border ring, divider light |
| `primary-300` | `#f09da5` | `240, 157, 165` | Hover secondary, badge ring |
| `primary-400` | `#e55a68` | `229, 90, 104` | Icon, outline button border |
| `primary-500` | `#d6192c` | `214, 25, 44` | Secondary button, link hover |
| `primary-600` | `#b80e20` | `184, 14, 32` | Primary CTA default, tab aktif |
| `primary-700` | **`#980416`** | **`152, 4, 22`** | **BASE — Brand utama** |
| `primary-800` | `#7a0513` | `122, 5, 19` | Nav aktif, hover dark |
| `primary-900` | `#5c040e` | `92, 4, 14` | Heading text gelap |
| `primary-950` | `#2e0105` | `46, 1, 5` | Text paling gelap |

### Neutral — Warm Gray

Warm gray dipilih agar selaras dengan maroon yang hangat.

| Token | Hex | RGB | Penggunaan |
|-------|-----|-----|-----------|
| `gray-50` | `#faf9f9` | `250, 249, 249` | Background halaman |
| `gray-100` | `#f4f2f2` | `244, 242, 242` | Background card, form input |
| `gray-200` | `#e5e2e2` | `229, 226, 226` | Border ring input, divider |
| `gray-300` | `#d0cccc` | `208, 204, 204` | Border card, separator |
| `gray-400` | `#a59f9f` | `165, 159, 159` | Placeholder text, disabled |
| `gray-500` | `#7a7375` | `122, 115, 117` | Secondary text, caption |
| `gray-600` | `#5c5557` | `92, 85, 87` | Body text |
| `gray-700` | `#3d383a` | `61, 56, 58` | Heading text |
| `gray-800` | `#262224` | `38, 34, 36` | Judul kuat, label |
| `gray-900` | `#141112` | `20, 17, 18` | Text paling gelap |

### Semantic

| Token | Hex | Penggunaan | Tailwind |
|-------|-----|-----------|----------|
| `success` | `#16a34a` | Berhasil, sudah diambil, aktif | `green-600` |
| `warning` | `#d97706` | Pending, peringatan, perlu review | `amber-600` |
| `danger` | `#dc2626` | Error, ditolak, hapus, gagal | `red-600` |
| `info` | `#2563eb` | Informasi, bantuan, link | `blue-600` |

### Mapping ke Tailwind Config

```js
// tailwind.config.js
colors: {
  primary: {
    50: '#fdf2f3',
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
  }
}
```

### Aturan Penggunaan Warna

| Elemen | Warna | Alasan |
|--------|-------|--------|
| Background halaman | `gray-50` | Netral, tidak bersaing dengan konten |
| Card/section | `white` dengan shadow ringan | Memisahkan konten secara visual |
| Primary CTA | `primary-700` | Brand identity |
| Hover CTA | `primary-800` | Feedback interaksi |
| Body text | `gray-600` | Kontras cukup, nyaman dibaca |
| Heading | `gray-800` atau `primary-900` | Lebih berat dari body |
| Success/active badge | `green` (semantic) | Standar universal |
| Error | `red` (semantic) | Standar universal |

---

## 3. Tipografi

### Font Stack

```css
font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
```

Inter dipilih karena sangat terbaca di ukuran kecil (mobile) dan punya banyak weight.

### Type Scale (Tailwind)

| Elemen | Class | Size | Weight | Penggunaan |
|--------|-------|------|--------|-----------|
| Display | `text-3xl md:text-4xl font-bold` | 30-36px | 700 | Hero, judul halaman |
| Heading 1 | `text-2xl font-bold` | 24px | 700 | Judul dashboard |
| Heading 2 | `text-xl font-semibold` | 20px | 600 | Judul section |
| Heading 3 | `text-lg font-semibold` | 18px | 600 | Judul card |
| Body | `text-sm` | 14px | 400 | Konten utama |
| Body small | `text-xs` | 12px | 400 | Caption, metadata |
| Button | `text-sm font-medium` | 14px | 500 | Semua tombol |
| Label | `text-xs font-medium` | 12px | 500 | Label form |
| Table header | `text-xs font-semibold uppercase tracking-wider` | 12px | 600 | Kolom tabel |

### Aturan Tipografi

- Jangan gunakan weight di bawah 400 untuk body text (keterbacaan)
- Line height: `leading-5` (20px) untuk body, `leading-6` (24px) untuk heading
- Tracking: `tracking-tight` untuk heading, normal untuk body
- Warna teks jangan lebih terang dari `gray-600` untuk body

---

## 4. Spacing & Layout

### Spacing Scale

Gunakan spacing bawaan Tailwind (4px base):

| Token | px | Penggunaan |
|-------|----|-----------|
| `p-2` | 8px | Padding dalam card kecil |
| `p-3` | 12px | Padding card |
| `p-4` | 16px | Padding card standar |
| `p-6` | 24px | Padding section |
| `gap-2` | 8px | Gap antar elemen form |
| `gap-3` | 12px | Gap antar button |
| `gap-4` | 16px | Gap antar card |
| `space-y-4` | 16px | Vertical stack form |
| `space-y-6` | 24px | Vertical antar section |

### Layout Patterns

#### Mobile (<768px)
```blade
<div class="flex flex-col space-y-4 px-4">
```

#### Tablet (768-1024px)
```blade
<div class="grid grid-cols-2 gap-4">
```

#### Desktop (>1024px)
```blade
<div class="grid grid-cols-3 gap-6">
```

### Container
```blade
{{-- Mobile: full width --}}
{{-- Desktop: max-width container --}}
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
```

---

## 5. Responsive Strategy

### Breakpoints

| Breakpoint | Tailwind | Target Role | Key Changes |
|-----------|----------|-------------|-------------|
| Mobile | `< sm` (640px) | Staff, Student | Bottom nav, single column, full-width actions |
| Mobile-L | `sm` (640px) | Staff, Student | Landscape improvements |
| Tablet | `md` (768px) | Transisi | Mulai sidebar, grid 2 kolom |
| Desktop | `lg` (1024px) | Admin, Super Admin | Sidebar tetap, tabel penuh |
| Desktop-L | `xl` (1280px) | Admin, Super Admin | Multi-column, detail panel |
| Wide | `2xl` (1536px) | Super Admin | Layar lebar maksimal |

### Perubahan Layout per Breakpoint

| Komponen | Mobile (<768px) | Desktop (≥1024px) |
|----------|----------------|-------------------|
| **Navigation** | Bottom tab bar (5 item) | Sidebar kiri (collapsible) |
| **Header** | Judul + back button | Breadcrumb + actions |
| **Tabel** | Card list horizontal scroll | Full table sticky header |
| **Form** | Stacked full width | Inline grid 2-3 kolom |
| **QR Scanner** | Fullscreen kamera | Modal popup 400px |
| **Modal** | Bottom sheet | Center modal max-w-lg |
| **Button group** | Stacked full width | Inline dengan gap |
| **Search** | Expandable icon | Persistent input |

### Mobile Patterns untuk Staff & Student

```blade
{{-- Bottom Navigation --}}
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
  <div class="flex justify-around items-center h-16">
    <a class="flex flex-col items-center text-xs text-primary-700">
      <span class="material-icons text-xl">home</span>
      <span>Beranda</span>
    </a>
    {{-- ... --}}
  </div>
</nav>

{{-- Full-width content dengan padding bottom untuk nav --}}
<main class="pb-20 px-4">
```

### Desktop Patterns untuk Admin & Super Admin

```blade
{{-- Layout dengan sidebar --}}
<div class="flex h-screen">
  {{-- Sidebar --}}
  <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0">
    {{-- ... --}}
  </aside>

  {{-- Main Content --}}
  <main class="flex-1 overflow-y-auto p-6">
  </main>
</div>
```

---

## 6. Navigation

### Bottom Navigation (Mobile — Staff & Student)

| Icon | Label | Route |
|------|-------|-------|
| `home` | Beranda | `/dashboard` |
| `qr_code_scanner` | Scan | `/scan` |
| `inventory` | Stok | `/stock` |
| `calendar_month` | Jadwal | `/schedule` |
| `person` | Akun | `/profile` |

**Aturan:**
- Maksimal 5 item
- Aktif: icon `primary-700` + label `text-primary-700 font-medium`
- Non-aktif: icon `gray-400` + label `text-gray-400`
- Tinggi: `h-16` (64px) — nyaman untuk jempol

### Sidebar Navigation (Desktop — Admin & Super Admin)

```
┌──────────────────────────────┐
│  Logo / Brand                │
├──────────────────────────────┤
│  ► Dashboard                 │
│  ► Master Data               │
│  ► Import                    │
│  ► Distribusi                │
│  ► Stock & Inventory         │
│  ► Stock Opname              │
│  ► GPM / Cost                │
│  ► Report                    │
├──────────────────────────────┤
│  ► Pengaturan                │
│  ► Logout                    │
└──────────────────────────────┘
```

**Aturan:**
- Lebar: `w-64` (256px) default, collapsible ke `w-16`
- Active item: background `primary-50` + text `primary-700` + border-left 3px `primary-700`
- Hover: background `gray-50`
- Group header: `text-xs font-semibold text-gray-400 uppercase tracking-wider`

### Breadcrumb (Desktop)

```blade
<nav class="flex text-sm text-gray-500 mb-4">
  <a href="" class="hover:text-primary-700">Dashboard</a>
  <span class="mx-2">/</span>
  <a href="" class="hover:text-primary-700">Master Data</a>
  <span class="mx-2">/</span>
  <span class="text-gray-800 font-medium">Fakultas</span>
</nav>
```

---

## 7. Komponen

### 7.1 Tombol

#### Primary Button
```blade
<button class="inline-flex items-center justify-center px-4 py-2 h-10
               bg-primary-700 text-white text-sm font-medium rounded-lg
               hover:bg-primary-800 active:bg-primary-900
               focus:outline-none focus:ring-2 focus:ring-primary-300
               disabled:opacity-50 disabled:cursor-not-allowed
               transition-colors duration-150">
  Simpan
</button>
```

#### Secondary Button (Outline)
```blade
<button class="inline-flex items-center justify-center px-4 py-2 h-10
               border border-primary-500 text-primary-700 text-sm font-medium rounded-lg
               hover:bg-primary-50 active:bg-primary-100
               focus:outline-none focus:ring-2 focus:ring-primary-300
               transition-colors duration-150">
  Batal
</button>
```

#### Ghost Button (Text Only)
```blade
<button class="inline-flex items-center justify-center px-3 py-2 h-10
               text-gray-600 text-sm font-medium rounded-lg
               hover:bg-gray-100 active:bg-gray-200
               focus:outline-none focus:ring-2 focus:ring-gray-300
               transition-colors duration-150">
  Hapus
</button>
```

#### Danger Button
```blade
<button class="inline-flex items-center justify-center px-4 py-2 h-10
               bg-red-600 text-white text-sm font-medium rounded-lg
               hover:bg-red-700 active:bg-red-800
               focus:outline-none focus:ring-2 focus:ring-red-300
               transition-colors duration-150">
  Hapus Data
</button>
```

#### Full-width Button (Mobile)
```blade
{{-- Tambahkan class w-full di mobile --}}
<button class="w-full sm:w-auto ...">
  Submit
</button>
```

#### Button Sizes
```blade
{{-- Small --}}
<button class="px-3 py-1.5 h-8 text-xs ...">Kecil</button>

{{-- Default --}}
<button class="px-4 py-2 h-10 text-sm ...">Standar</button>

{{-- Large (mobile CTA) --}}
<button class="px-6 py-3 h-12 text-base ...">Besar</button>
```

### 7.2 Card

```blade
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
  {{-- konten --}}
</div>

{{-- Card dengan header --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
  <div class="px-4 py-3 border-b border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800">Judul Card</h3>
  </div>
  <div class="p-4">
    {{-- konten --}}
  </div>
</div>

{{-- Card klik (untuk mobile list) --}}
<a class="block bg-white rounded-xl shadow-sm border border-gray-200 p-4
          active:bg-gray-50 transition-colors">
  {{-- isi card --}}
</a>
```

### 7.3 Badge & Status

```blade
{{-- Status umum --}}
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
             bg-green-100 text-green-800">
  Aktif
</span>

{{-- Variant per status --}}
@php
  $styles = [
    'completed' => 'bg-green-100 text-green-800',
    'pending' => 'bg-yellow-100 text-yellow-800',
    'cancelled' => 'bg-red-100 text-red-800',
    'draft' => 'bg-gray-100 text-gray-800',
    'partial' => 'bg-blue-100 text-blue-800',
  ];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
             {{ $styles[$status] }}">
  {{ $statusLabel }}
</span>
```

### 7.4 Modal

#### Bottom Sheet (Mobile)
```blade
{{-- Overlay --}}
<div class="fixed inset-0 bg-black/50 z-40" x-show="open" @click="open = false"></div>

{{-- Sheet --}}
<div class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-xl z-50
            transform transition-transform duration-300"
     x-show="open"
     x-transition:enter="translate-y-full"
     x-transition:enter-end="translate-y-0">
  {{-- Handle --}}
  <div class="flex justify-center pt-2 pb-1">
    <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
  </div>
  {{-- Konten --}}
  <div class="p-4 max-h-[80vh] overflow-y-auto">
    {{ $slot }}
  </div>
</div>
```

#### Center Modal (Desktop)
```blade
<div class="fixed inset-0 bg-black/50 z-40" x-show="open"></div>

<div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-show="open">
  <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-semibold text-gray-800">Judul</h2>
      <button @click="open = false" class="text-gray-400 hover:text-gray-600">
        <span class="text-xl">x</span>
      </button>
    </div>
    {{ $slot }}
  </div>
</div>
```

### 7.5 Loading & Skeleton

```blade
{{-- Loading spinner --}}
<div class="flex items-center justify-center py-8">
  <svg class="animate-spin h-8 w-8 text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
  </svg>
</div>

{{-- Skeleton card --}}
<div class="animate-pulse bg-white rounded-xl border border-gray-200 p-4">
  <div class="h-4 bg-gray-200 rounded w-3/4 mb-3"></div>
  <div class="h-3 bg-gray-200 rounded w-1/2 mb-2"></div>
  <div class="h-3 bg-gray-200 rounded w-full"></div>
</div>

{{-- Skeleton table row --}}
<tr class="animate-pulse">
  <td class="px-4 py-3"><div class="h-3 bg-gray-200 rounded w-24"></div></td>
  <td class="px-4 py-3"><div class="h-3 bg-gray-200 rounded w-32"></div></td>
  <td class="px-4 py-3"><div class="h-6 bg-gray-200 rounded-full w-16"></div></td>
  <td class="px-4 py-3"><div class="h-3 bg-gray-200 rounded w-12"></div></td>
</tr>
```

### 7.6 Empty State

```blade
<div class="flex flex-col items-center justify-center py-12 text-center">
  <div class="text-gray-300 text-5xl mb-4">
    {{-- Icon --}}
    <svg class="w-16 h-16 mx-auto" ...></svg>
  </div>
  <h3 class="text-lg font-semibold text-gray-700 mb-1">Belum Ada Data</h3>
  <p class="text-sm text-gray-500 mb-4">Belum ada mahasiswa yang diimport.</p>
  <a href="{{ route('import') }}" class="btn-primary">Import Data</a>
</div>
```

### 7.7 QR Display

```blade
{{-- QR Card untuk mahasiswa --}}
<div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
  <div class="inline-block p-3 bg-white rounded-lg shadow-sm mb-3">
    {!! $qrSvg !!}
  </div>
  <p class="text-sm font-medium text-gray-800">{{ $student->name }}</p>
  <p class="text-xs text-gray-500">{{ $student->nim }}</p>
  <p class="text-xs text-gray-400 mt-2">QR ini berlaku seumur hidup</p>
</div>

{{-- QR dalam list (mobile) --}}
<div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200">
  <div class="w-16 h-16 flex-shrink-0">
    {!! QrCode::size(64)->generate($student->qr_token) !!}
  </div>
  <div class="flex-1 min-w-0">
    <p class="text-sm font-medium text-gray-800 truncate">{{ $student->name }}</p>
    <p class="text-xs text-gray-500">{{ $student->nim }}</p>
  </div>
</div>
```

---

## 8. Form & Validasi

### 8.1 Text Input

```blade
{{-- Default --}}
<div class="space-y-1">
  <label class="text-xs font-medium text-gray-700">Nama Lengkap</label>
  <input type="text"
         class="w-full px-3 py-2 h-10 text-sm
                bg-gray-100 border border-gray-200 rounded-lg
                text-gray-800 placeholder-gray-400
                focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100
                disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed
                transition-colors duration-150"
         placeholder="Masukkan nama">
  <p class="text-xs text-gray-400">Helper text opsional</p>
</div>
```

### 8.2 Validation States

```blade
{{-- Normal --}}
<input class="border-gray-200 ...">

{{-- Focus --}}
<input class="border-primary-500 ring-2 ring-primary-100 ...">

{{-- Valid / Sukses --}}
<input class="border-green-500 ring-2 ring-green-100 bg-white ...">
@if ($message)
  <p class="text-xs text-green-600 flex items-center gap-1 mt-1">
    <span>✓</span> {{ $message }}
  </p>
@endif

{{-- Error --}}
<input class="border-red-500 ring-2 ring-red-100 bg-white ...">
@error('field')
  <p class="text-xs text-red-600 flex items-center gap-1 mt-1">
    <span>✕</span> {{ $message }}
  </p>
@enderror

{{-- Disabled --}}
<input class="bg-gray-50 text-gray-400 border-gray-100 cursor-not-allowed ..." disabled>

{{-- Loading / Skeleton --}}
<div class="animate-pulse h-10 bg-gray-200 rounded-lg"></div>
```

### 8.3 Label & Floating Label

```blade
{{-- Standard label --}}
<label class="block text-xs font-medium text-gray-700 mb-1">
  Nama Lengkap
  @if($required) <span class="text-red-500">*</span> @endif
</label>

{{-- Floating label (mobile friendly) --}}
<div class="relative">
  <input type="text" id="nama"
         class="peer w-full px-3 pt-5 pb-2 h-12 text-sm
                border border-gray-200 rounded-lg
                focus:border-primary-500 focus:ring-2 focus:ring-primary-100
                transition-colors"
         placeholder=" "
         value="{{ old('nama') }}">
  <label for="nama"
         class="absolute left-3 top-1 text-xs text-gray-400
                peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-sm
                peer-focus:top-1 peer-focus:text-xs peer-focus:text-primary-700
                transition-all duration-150">
    Nama Lengkap
  </label>
</div>
```

### 8.4 Select / Dropdown

```blade
<select class="w-full px-3 py-2 h-10 text-sm
              bg-gray-100 border border-gray-200 rounded-lg
              text-gray-800
              focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100
              appearance-none
              transition-colors duration-150">
  <option value="" disabled selected>Pilih Fakultas</option>
  <option value="1">FKIP</option>
  <option value="2">FEB</option>
</select>
```

### 8.5 Radio Button (Card Style — untuk pilih ukuran)

```blade
{{-- Card radio untuk size chart --}}
<label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200
              has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50
              cursor-pointer transition-colors">
  <input type="radio" name="size" value="M"
         class="w-4 h-4 text-primary-700 border-gray-300
                focus:ring-primary-500">
  <div>
    <p class="text-sm font-medium text-gray-800">M</p>
    <p class="text-xs text-gray-500">Lebar 52cm, Panjang 70cm</p>
  </div>
</label>
```

### 8.6 Checkbox

```blade
{{-- Single checkbox --}}
<label class="flex items-center gap-2 cursor-pointer">
  <input type="checkbox"
         class="w-4 h-4 text-primary-700 border-gray-300 rounded
                focus:ring-primary-500">
  <span class="text-sm text-gray-700">Setuju dengan syarat & ketentuan</span>
</label>

{{-- Checklist item distribusi (Staff) --}}
<label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200
              has-[:checked]:border-green-500 has-[:checked]:bg-green-50
              cursor-pointer transition-colors">
  <input type="checkbox"
         class="w-5 h-5 text-green-600 border-gray-300 rounded
                focus:ring-green-500">
  <div class="flex-1">
    <p class="text-sm font-medium text-gray-800">Kemeja Putih</p>
    <p class="text-xs text-gray-500">Ukuran: M | Stok: 12</p>
  </div>
  <span class="text-xs text-gray-400">Qty: 1</span>
</label>
```

### 8.7 File Upload (Import Excel)

```blade
{{-- Drag & drop zone --}}
<div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center
            hover:border-primary-400 hover:bg-primary-50/30
            transition-colors cursor-pointer"
     x-data="{ dragging: false }"
     x-on:dragover.prevent="dragging = true"
     x-on:dragleave="dragging = false"
     x-on:drop.prevent="dragging = false"
     :class="{ 'border-primary-500 bg-primary-50': dragging }">

  <div class="text-gray-400 text-4xl mb-2">
    <svg class="w-10 h-10 mx-auto" ...>upload icon</svg>
  </div>
  <p class="text-sm text-gray-600 mb-1">
    <span class="text-primary-700 font-medium">Klik untuk upload</span> atau drag & drop
  </p>
  <p class="text-xs text-gray-400">Format: .xlsx, .xls, .csv (Maks 5MB)</p>
  <input type="file" accept=".xlsx,.xls,.csv" class="hidden">

  {{-- Preview setelah pilih file --}}
  <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg text-left">
    <p class="text-sm text-green-800 font-medium">File siap: data_mahasiswa.xlsx</p>
    <p class="text-xs text-green-600">1.250 baris ditemukan</p>
  </div>
</div>
```

### 8.8 Search Input

```blade
{{-- Search mobile (expandable) --}}
<div class="relative" x-data="{ open: false }">
  <button @click="open = !open" class="p-2 text-gray-500">
    <svg class="w-5 h-5">search icon</svg>
  </button>
  <input x-show="open"
         type="search"
         class="w-full px-3 py-2 h-10 text-sm
                bg-gray-100 border border-gray-200 rounded-lg
                focus:bg-white focus:border-primary-500
                transition-all"
         placeholder="Cari NIM atau nama...">
</div>

{{-- Search desktop (persistent) --}}
<div class="relative">
  <input type="search"
         class="w-72 pl-9 pr-3 py-2 h-10 text-sm
                bg-gray-100 border border-gray-200 rounded-lg
                focus:bg-white focus:border-primary-500
                transition-colors"
         placeholder="Cari NIM atau nama...">
  <span class="absolute left-3 top-2.5 text-gray-400">
    <svg class="w-4 h-4">search icon</svg>
  </span>
</div>
```

### 8.9 OTP Input

```blade
{{-- 6 digit OTP boxes --}}
<div class="flex gap-2 justify-center" x-data>
  <template x-for="i in 6" :key="i">
    <input type="text" maxlength="1"
           class="w-10 h-12 text-center text-lg font-semibold
                  bg-gray-100 border border-gray-200 rounded-lg
                  focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100
                  transition-colors"
           x-on:input="if($event.target.value) $event.target.nextElementSibling?.focus()"
           x-on:keydown.backspace="if(!$event.target.value) $event.target.previousElementSibling?.focus()">
  </template>
</div>
<p class="text-xs text-gray-500 text-center mt-2">Kode OTP dikirim ke email kampus</p>
```

### 8.10 Password Input

```blade
<div class="space-y-1" x-data="{ show: false }">
  <label class="text-xs font-medium text-gray-700">Password</label>
  <div class="relative">
    <input :type="show ? 'text' : 'password'"
           class="w-full px-3 py-2 h-10 text-sm pr-10
                  bg-gray-100 border border-gray-200 rounded-lg
                  focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100
                  transition-colors">
    <button type="button" @click="show = !show"
            class="absolute right-2 top-2.5 text-gray-400 hover:text-gray-600">
      <span x-text="show ? 'hide' : 'show'" class="text-xs">show</span>
    </button>
  </div>
</div>
```

### 8.11 Textarea

```blade
<textarea rows="3"
          class="w-full px-3 py-2 text-sm
                 bg-gray-100 border border-gray-200 rounded-lg
                 text-gray-800 placeholder-gray-400
                 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100
                 transition-colors"
          placeholder="Catatan...">{{ old('notes') }}</textarea>
```

### 8.12 Form Layout Patterns

#### Mobile (stacked)
```blade
<form class="space-y-4">
  <div>input 1</div>
  <div>input 2</div>
  <div>input 3</div>
  <button type="submit" class="w-full btn-primary">Submit</button>
</form>
```

#### Desktop (inline grid)
```blade
<form class="grid grid-cols-2 gap-4">
  <div>input 1</div>
  <div>input 2</div>
  <div class="col-span-2">input 3 (full width)</div>
  <div class="col-span-2 flex justify-end gap-3">
    <button type="button" class="btn-secondary">Batal</button>
    <button type="submit" class="btn-primary">Simpan</button>
  </div>
</form>
```

#### Multi-step Wizard (Import)
```blade
{{-- Step indicator --}}
<nav class="flex mb-6">
  <div class="flex-1 text-center">
    <div class="w-8 h-8 mx-auto rounded-full bg-primary-700 text-white text-sm font-medium flex items-center justify-center">1</div>
    <p class="text-xs mt-1 text-primary-700 font-medium">Upload</p>
  </div>
  <div class="flex-1 text-center">
    <div class="w-8 h-8 mx-auto rounded-full bg-gray-200 text-gray-500 text-sm font-medium flex items-center justify-center">2</div>
    <p class="text-xs mt-1 text-gray-400">Preview</p>
  </div>
  <div class="flex-1 text-center">
    <div class="w-8 h-8 mx-auto rounded-full bg-gray-200 text-gray-500 text-sm font-medium flex items-center justify-center">3</div>
    <p class="text-xs mt-1 text-gray-400">Konfirmasi</p>
  </div>
</nav>
```

---

## 9. Tabel Data

### Mobile (Card List)

```blade
<div class="space-y-3">
  @foreach ($students as $student)
    <a href="{{ route('students.show', $student) }}"
       class="block bg-white rounded-xl border border-gray-200 p-4
              active:bg-gray-50 transition-colors">
      <div class="flex items-center justify-between mb-1">
        <p class="text-sm font-medium text-gray-800">{{ $student->name }}</p>
        <span class="text-xs text-green-600 font-medium">Aktif</span>
      </div>
      <p class="text-xs text-gray-500">{{ $student->nim }} | {{ $student->studyProgram->name }}</p>
    </a>
  @endforeach
</div>
```

### Desktop (Full Table)

```blade
<div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
  <table class="w-full text-sm">
    <thead>
      <tr class="bg-gray-50 border-b border-gray-200">
        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NIM</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Prodi</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      <tr class="hover:bg-gray-50 transition-colors">
        <td class="px-4 py-3 text-gray-800 font-medium">2024001</td>
        <td class="px-4 py-3 text-gray-600">Andi Pratama</td>
        <td class="px-4 py-3 text-gray-500">Teknik Informatika</td>
        <td class="px-4 py-3"><span class="badge badge-success">Aktif</span></td>
        <td class="px-4 py-3 text-right">
          <a href="#" class="text-primary-700 hover:text-primary-800 text-sm font-medium">Detail</a>
        </td>
      </tr>
    </tbody>
  </table>
</div>

{{-- Pagination --}}
<div class="flex items-center justify-between mt-4">
  <p class="text-xs text-gray-500">Menampilkan 1-10 dari 125 data</p>
  <div class="flex gap-1">
    <button class="px-3 py-1 text-sm border rounded-lg disabled:opacity-50">Sebelumnya</button>
    <button class="px-3 py-1 text-sm border rounded-lg bg-primary-700 text-white">1</button>
    <button class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50">2</button>
    <button class="px-3 py-1 text-sm border rounded-lg">Selanjutnya</button>
  </div>
</div>
```

---

## 10. QR Scanner

### Mobile (Fullscreen)

```blade
<div class="fixed inset-0 bg-black z-40 flex flex-col">
  {{-- Header --}}
  <div class="flex items-center justify-between px-4 py-3 bg-black/80">
    <button onclick="stopScanner()" class="text-white text-sm">Batal</button>
    <p class="text-white text-sm font-medium">Scan QR Mahasiswa</p>
    <button onclick="toggleTorch()" class="text-white text-sm">Senter</button>
  </div>

  {{-- Scanner --}}
  <div class="flex-1 flex items-center justify-center">
    <div id="reader" class="w-64 h-64"></div>
  </div>

  {{-- Fallback --}}
  <div class="px-4 py-6 bg-white rounded-t-2xl">
    <p class="text-xs text-gray-500 text-center mb-3">QR tidak bisa dipindai?</p>
    <form action="{{ route('scan.manual') }}" method="POST" class="flex gap-2">
      @csrf
      <input type="text" name="nim" placeholder="Ketik NIM manual"
             class="flex-1 px-3 py-3 h-12 text-sm border border-gray-200 rounded-lg">
      <button type="submit" class="px-6 h-12 bg-primary-700 text-white font-medium rounded-lg">Cari</button>
    </form>
  </div>
</div>
```

### Desktop (Modal)

```blade
{{-- Trigger button --}}
<button onclick="openScanner()"
        class="inline-flex items-center gap-2 px-4 py-2 h-10
               bg-primary-700 text-white text-sm font-medium rounded-lg">
  <span>Scan QR</span>
</button>

{{-- Modal scanner --}}
<div id="scanner-modal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/50" onclick="closeScanner()"></div>
  <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
              bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-semibold">Scan QR</h3>
      <button onclick="closeScanner()" class="text-gray-400 hover:text-gray-600">x</button>
    </div>
    <div id="reader" class="w-full aspect-square"></div>
    <div class="mt-4">
      <form action="{{ route('scan.manual') }}" method="POST" class="flex gap-2">
        @csrf
        <input type="text" name="nim" placeholder="Atau ketik NIM"
               class="flex-1 px-3 py-2 h-10 text-sm border border-gray-200 rounded-lg">
        <button type="submit" class="px-4 h-10 bg-gray-100 text-gray-700 rounded-lg text-sm">Cari</button>
      </form>
    </div>
  </div>
</div>
```

---

## 11. Status & Feedback

### 11.1 Toast / Notifikasi

```blade
{{-- Success --}}
<div class="fixed top-4 right-4 z-50 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg
            flex items-center gap-2 text-sm"
     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
  <span>✓</span>
  <span>Data berhasil disimpan</span>
  <button @click="show = false" class="ml-2 text-white/80 hover:text-white">x</button>
</div>

{{-- Error --}}
<div class="fixed top-4 right-4 z-50 bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg
            flex items-center gap-2 text-sm">
  <span>✕</span>
  <span>Gagal menyimpan data</span>
</div>

{{-- Warning --}}
<div class="fixed top-4 right-4 z-50 bg-yellow-500 text-white px-4 py-3 rounded-lg shadow-lg
            flex items-center gap-2 text-sm">
  <span>!</span>
  <span>Stok tersisa 2 item</span>
</div>
```

### 11.2 Inline Error (Form)

```blade
@error('nim')
  <p class="text-xs text-red-600 flex items-center gap-1 mt-1">
    <span>✕</span> {{ $message }}
  </p>
@enderror
```

### 11.3 Alert Banner

```blade
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-start gap-3">
  <span class="text-yellow-600 text-lg flex-shrink-0">!</span>
  <div>
    <p class="text-sm font-medium text-yellow-800">Perhatian</p>
    <p class="text-xs text-yellow-700">{{ $message }}</p>
  </div>
</div>
```

### 11.4 Submit Button Loading State

```blade
<button type="submit" x-data="{ loading: false }"
        x-on:click="loading = true"
        :disabled="loading"
        class="btn-primary">
  <span x-show="!loading">Simpan</span>
  <span x-show="loading" class="flex items-center gap-2">
    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
    </svg>
    Menyimpan...
  </span>
</button>
```

---

## 12. Halaman Contoh

### Student — Dashboard Mobile

```
┌──────────────────────────┐
│ [back] Dashboard    [🔔] │  ← header
├──────────────────────────┤
│ ┌──────────────────────┐ │
│ │ 👤 Andi Pratama      │ │
│ │ 2024001              │ │
│ │ Teknik Informatika   │ │
│ └──────────────────────┘ │
│                          │
│ ┌───┐ ┌───┐ ┌───┐       │
│ │📋 │ │👕 │ │📅 │       │
│ │   │ │   │ │   │       │
│ │UK |UKURAN|JADWAL│       │
│ └───┘ └───┘ └───┘       │
│                          │
│ Jadwal Terdekat          │
│ ┌──────────────────────┐ │
│ │ Tahap 1: Almamater   │ │
│ │ 📅 25 Jul 2026       │ │
│ │ 📍 Hall A            │ │
│ │ 🟢 Sudah Diambil     │ │
│ └──────────────────────┘ │
│ ┌──────────────────────┐ │
│ │ Tahap 2: PDH         │ │
│ │ 📅 1 Aug 2026        │ │
│ │ 📍 Hall B            │ │
│ │ ⏳ Belum Diambil     │ │
│ └──────────────────────┘ │
├──────────────────────────┤
│ 🏠  🔍  📦  📅  👤     │  ← bottom nav
└──────────────────────────┘
```

### Staff — Scan QR Mobile

```
┌──────────────────────────┐
│   ⚪⚪⚪⚪⚪⚪⚪⚪⚪       │  ← kamera QR
│   ⚪  ┌──────────┐  ⚪   │
│   ⚪  │  SCAN QR  │  ⚪   │
│   ⚪  │          │  ⚪   │
│   ⚪  └──────────┘  ⚪   │
│   ⚪⚪⚪⚪⚪⚪⚪⚪⚪       │
├──────────────────────────┤
│ Atau ketik NIM:          │
│ [____Cari NIM________]   │
│                          │
│ [🔍 Cari Manual]         │
└──────────────────────────┘
```

Setelah scan berhasil:

```
┌──────────────────────────┐
│ [✕ Tutup]               │
├──────────────────────────┤
│ 👤 Andi Pratama          │
│ NIM: 2024001             │
│ Prodi: Teknik Informatika│
│ Tahap: Almamater ✅      │
├──────────────────────────┤
│ ☑️ Kemeja Putih    M  ✓ │
│ ☑️ Celana Hitam    L  ✓ │
│ ☑️ Dasi            F  ✓ │
│ ☑️ Peci/Lisensi    L  ✓ │
│ ☑️ Sepatu         42    │  ← centang semua
├──────────────────────────┤
│ [✓ Konfirmasi Ambil]     │  ← tombol besar hijau
└──────────────────────────┘
```

### Admin — Import Desktop

```
┌───────┬──────────────────────────────────────────┐
│       │  Dashboard > Import > Mahasiswa           │
│       │                                           │
│  ▶️   │  ┌─ Step 1: Upload ── Step 2 ── Step 3 ─┐│
│  Dasb │  │  🟢 ● ────────── ○ ────────── ○     ││
│       │  └────────────────────────────────────────┘│
│  ▶️   │                                           │
│  Mastr│  ┌──[📁 Upload File]───────────────────┐  │
│       │  │   Drag & drop file Excel di sini    │  │
│  ▶️   │  │   atau klik untuk upload             │  │
│  Imprt│  │   Format: .xlsx (Maks 5MB)          │  │
│       │  └──────────────────────────────────────┘  │
│  ▶️   │                                           │
│  Distr│  ┌── Preview ──────────────────────────┐  │
│       │  │  File: data_mahasiswa_2026.xlsx     │  │
│  ▶️   │  │  1.250 baris ditemukan              │  │
│  Stok │  │  1.240 valid     10 error           │  │
│       │  └──────────────────────────────────────┘  │
│  ▶️   │                                           │
│  Rp   │  ┌─────────────────┐  ┌────────────────┐  │
│  GPM  │  │ [⬅️ Kembali]   │  │ [✓ Lanjutkan] │  │
│       │  └─────────────────┘  └────────────────┘  │
│  ▶️   │                                           │
│  Rprt │                                           │
└───────┴──────────────────────────────────────────┘
```

### Super Admin — Users Desktop

```
┌───────┬──────────────────────────────────────────┐
│       │  Pengaturan > Users                      │
│       │  [🔍 Cari user...]  [+ Tambah User]      │
│  ▶️   │                                           │
│  Dasb │  ┌── Tabel Users ───────────────────────┐ │
│       │  │ Nama       Email          Role  Aksi │ │
│  ▶️   │  ├──────────────────────────────────────┤ │
│  Mastr│  │ Budi    budi@...    Admin  ✏️ 🗑️  │ │
│       │  │ Sari    sari@...    Staff    ✏️ 🗑️  │ │
│  ▶️   │  │ Andi    andi@...    Student  ✏️ 🗑️  │ │
│  Users│  │ Admin   admin@...   Super    ✏️ 🗑️  │ │
│       │  └──────────────────────────────────────┘ │
│  ▶️   │                                           │
│  Confg│  Page: ‹ 1 2 3 ... 10 ›                  │
│       │                                           │
│  ▶️   │  ┌── Role Distribution ────────────────┐  │
│  Audit│  │  🟣 Super Admin: 2                  │  │
│       │  │  🔵 Admin:      5                 │  │
│  ▶️   │  │  🟠 Staff:       12                 │  │
│  Bckup│  │  🟢 Student:   1.250               │  │
│       │  └──────────────────────────────────────┘  │
└───────┴──────────────────────────────────────────┘
```

---

## Panduan Implementasi

### Cara Menggunakan Panduan Ini

1. **Developer Backend** — gunakan untuk patokan nama class Tailwind di Blade
2. **Developer Frontend** — jadikan referensi komponen dan pattern
3. **Designer** — acuan warna, spacing, tipografi

### Tailwind Config Minimal

```js
// tailwind.config.js
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#fdf2f3',
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
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
```

### Checklist Penerapan

- [ ] Tailwind config sudah include custom colors
- [ ] Inter font sudah di-load (via Google Fonts atau download)
- [ ] Bottom navigation untuk mobile (Staff & Student)
- [ ] Sidebar untuk desktop (Admin & Super Admin)
- [ ] Form validation states (default, focus, valid, error, disabled)
- [ ] Loading skeleton untuk tabel & card
- [ ] Toast notifikasi global (success, error, warning)
- [ ] QR Scanner fullscreen untuk mobile, modal untuk desktop
- [ ] Responsive breakpoints di semua halaman
