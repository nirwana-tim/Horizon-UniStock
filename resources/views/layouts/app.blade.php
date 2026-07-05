<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Horizon UniStock') }}</title>
    <meta name="description" content="Sistem Distribusi Seragam & Inventory Management — Horizon UniStock">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">

@php
    $isSidebarLayout = auth()->check() && auth()->user()->hasAnyRole(['super_admin', 'admin', 'finance', 'student']);
    $isBottomNavLayout = auth()->check() && auth()->user()->hasAnyRole(['staff']);
@endphp

@if($isSidebarLayout)
    {{-- ===== SIDEBAR LAYOUT (Admin & Super Admin) ===== --}}
    <div class="flex h-screen overflow-hidden bg-gray-50">

        {{-- Sidebar --}}
        <x-sidebar />

        {{-- Main area --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            {{-- Topbar --}}
            <x-topbar />

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto custom-scroll p-6">

                {{-- Session Flash Messages --}}
                @if(session('success'))
                    <x-alert type="success">{{ session('success') }}</x-alert>
                @endif
                @if(session('error'))
                    <x-alert type="error">{{ session('error') }}</x-alert>
                @endif
                @if(session('warning'))
                    <x-alert type="warning">{{ session('warning') }}</x-alert>
                @endif
                @if(session('info'))
                    <x-alert type="info">{{ session('info') }}</x-alert>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

@elseif($isBottomNavLayout)
    {{-- ===== BOTTOM NAV LAYOUT (Staff & Student) ===== --}}
    <div class="min-h-screen bg-gray-50 pb-20">

        {{-- Simple topbar --}}
        <x-topbar :simple="true" />

        {{-- Page Content --}}
        <main class="px-4 py-5">

            {{-- Session Flash Messages --}}
            @if(session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif
            @if(session('error'))
                <x-alert type="error">{{ session('error') }}</x-alert>
            @endif
            @if(session('warning'))
                <x-alert type="warning">{{ session('warning') }}</x-alert>
            @endif
            @if(session('info'))
                <x-alert type="info">{{ session('info') }}</x-alert>
            @endif

            {{ $slot }}
        </main>

        {{-- Bottom Navigation --}}
        <x-bottom-nav />
    </div>

@else
    {{-- ===== FALLBACK LAYOUT ===== --}}
    <div class="min-h-screen bg-gray-50">
        <main class="p-6">
            {{ $slot }}
        </main>
    </div>
@endif

@stack('scripts')
</body>
</html>
