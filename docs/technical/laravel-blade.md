# Laravel 12 + Blade

## Apa Itu Laravel 12?

Laravel 12 adalah framework PHP untuk membangun web app. Blade adalah template engine bawaan Laravel yang memungkinkan Anda menulis HTML dengan logika PHP sederhana.

## Fitur Blade yg Terinstall di UniStock

| Fitur | Untuk Apa |
|-------|-----------|
| Blade Component | Layout via `<x-app-layout>`, reusable UI (`<x-input-label>`, `<x-primary-button>`, `<x-alert>`, `<x-badge>`, `<x-page-header>`, `<x-stat-card>`, `<x-empty-state>`, `<x-delete-modal>`) |
| Blade Directive | `@auth`, `@guest`, `@if`, `@foreach`, `@role`, `@hasrole`, `@vite` |
| Slots | Kirim konten ke component (`{{ $slot }}`, `{{ $header }}`) |
| Vite Integration | `@vite()` directive buat load CSS/JS build |
| Alpine.js | Komponen interaktif via `x-data`, `x-show`, `@click`, dst. |

## 1. Component-Based Layout (multi-role)

Proyek memakai **component pattern** dengan 3 layout yang dipilih otomatis berdasarkan role user:

| Role | Layout | Navigasi |
|------|--------|----------|
| `super_admin` / `admin` / `staff` | **Sidebar** (`x-sidebar` + `x-topbar`), desktop-first | `components/sidebar.blade.php` (staff juga dapat `x-bottom-nav` di layar kecil) |
| `student` | **Bottom Tab Bar** (mobile-first) | `components/bottom-nav.blade.php` |

**`resources/views/layouts/app.blade.php`** (layout utama):
```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>window.DASHBOARD_URL = @json(route('dashboard'));</script>
</head>
<body>
    @php
        $isSidebarLayout = auth()->user()->hasAnyRole(['super_admin', 'admin', 'staff']);
        $isBottomNavLayout = auth()->user()->hasRole('student');
    @endphp

    @if($isSidebarLayout)
        <x-sidebar />
        <x-topbar />
        <main>{{ $header ?? '' }}{{ $slot }}</main>
    @elseif($isBottomNavLayout)
        <x-topbar :simple="true" />
        <main>{{ $header ?? '' }}{{ $slot }}</main>
        <x-bottom-nav />
    @endif
</body>
</html>
```

**Halaman yg pake layout:**
```blade
<x-app-layout>
    <x-page-header title="Dashboard" />

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        ...
    </div>
</x-app-layout>
```

> Untuk halaman tanpa login (auth): pakai layout tersendiri (`layouts/auth` atau guest layout). Register **tidak aktif** di proyek ini — akun dibuat via Generate Akun (Admin) / seeder.

## 2. Blade Component

**`resources/views/components/alert.blade.php`**
```blade
@props(['type' => 'info'])

<div class="alert alert-{{ $type }}">
    {{ $slot }}
</div>
```

**Pake di view:**
```blade
<x-alert type="success">Data berhasil disimpan</x-alert>
```

## 3. Blade Directive yg Sering Dipake

```blade
{{-- Kondisi --}}
@if (Auth::check())
    @auth
        {{-- user login --}}
    @endauth
@else
    @guest
        {{-- belum login --}}
    @endguest
@endif

{{-- Looping --}}
@foreach ($items as $item)
    {{ $item->name }}
@endforeach

{{-- Form CSRF --}}
@csrf
```

## 4. Vite + Asset

```blade
{{-- di head --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- atau spesifik page --}}
@vite('resources/js/pages/dashboard.js')
```

## 5. Struktur Folder View

```
resources/views/
├── layouts/          # Layout utama (app.blade.php multi-role)
├── components/       # Blade component reusable (28: sidebar, topbar, bottom-nav, alert, badge, page-header, stat-card, empty-state, delete-modal, dll.)
├── auth/             # Login, forgot-password, reset-password, verify-email-otp, confirm-password, change-password
├── profile/          # Edit profile
├── dashboards/       # Dashboard per role (super-admin, admin, staff, student)
├── master/           # Master data CRUD (faculty, study-program, student, item, vendor, dll.)
├── distribution/     # Entitlement, distribution-schedule, size-events, size-monitor, scan
├── inventory/        # Stock receive, stock balance, stock movement, stock opname
├── finance/          # Eligibility, GPM, size change event
├── report/           # Laporan & GPM
├── student/          # Student self-service (size input, QR)
├── import/           # Import data + hasil
├── emails/           # Template email (OTP, distribusi)
├── errors/           # Halaman error (404, 403, 500)
└── system/           # SMTP settings, user management (super admin)
```

## 6. Blade Best Practices

- Gunakan component (`<x- >`) untuk UI reusable, jangan copy-paste HTML
- `{{ $slot }}` untuk konten utama, `<x-slot name="...">` untuk slot bernama
- `@vite()` di head, bukan di body
- Hindari PHP logic di view — pindahkan ke controller/service
- Gunakan `{{ $var }}` (escaped) bukan `{!! $var !!}` (raw) kecuali perlu HTML

## Sumber
- https://laravel.com/docs/13.x/blade
- https://laravel.com/docs/13.x/vite

## Analogi
Blade itu seperti cetakan kue — Anda bikin cetakan (layout) sekali, tinggal tuang adonan (konten) berbeda tiap halaman.
