# Laravel 13 + Blade

## Apa Itu Laravel 13?

Laravel 13 adalah framework PHP untuk membangun web app. Blade adalah template engine bawaan Laravel yang memungkinkan Anda menulis HTML dengan logika PHP sederhana.

## Fitur Blade yg Terinstall di Horizon-UniStock

| Fitur | Untuk Apa |
|-------|-----------|
| Blade Component | Layout via `<x-app-layout>`, reusable UI (`<x-input-label>`, `<x-primary-button>`) |
| Blade Directive | `@auth`, `@guest`, `@if`, `@foreach`, `@section`, `@yield` |
| Slots | Kirim konten ke component (`{{ $slot }}`, `<x-slot name="header">`) |
| Vite Integration | `@vite()` directive buat load CSS/JS build |

## 1. Component-Based Layout (Breeze Pattern)

Breeze Blade Stack pake **component pattern**, bukan `@extends`:

**`resources/views/layouts/app.blade.php`** (layout utama):
```blade
<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name', 'Horizon-UniStock') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('layouts.navigation')

    <main>
        {{ $slot }}
    </main>
</body>
</html>
```

**Halaman yg pake layout:**
```blade
<x-app-layout>
    <x-slot name="header">
        <h2>Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <h1>Selamat Datang</h1>
    </div>
</x-app-layout>
```

> Atau pake `<x-guest-layout>` untuk halaman tanpa navbar (login, register).

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
├── layouts/          # Layout utama (app, guest)
├── components/       # Blade component reusable
├── auth/             # Login, register, forgot-password
├── profile/          # Edit profile
├── partials/         # Potongan kecil blade (navbar)
├── vendor/           # Override package views
├── dashboard.blade.php
└── welcome.blade.php
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
