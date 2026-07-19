<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'UniStock') }}</title>
    <meta name="description" content="Uniform Distribution & Inventory Management System — UniStock">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
@php
    $isSidebarLayout = auth()->check() && auth()->user()->hasAnyRole(['super_admin', 'admin', 'staff']);
    $isBottomNavLayout = auth()->check() && auth()->user()->hasRole('student');
@endphp
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800 {{ $isSidebarLayout ? 'h-screen overflow-hidden' : '' }}">

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
            <main class="flex-1 overflow-y-auto overflow-x-hidden custom-scroll p-6 @role('staff') pb-20 lg:pb-6 @endrole">

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

                @if(session('generated_passwords'))
                    <div x-data="{ showPasswords: false }" class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl" x-init="showPasswords = true">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm text-yellow-800 font-medium">
                                <strong>Perhatian:</strong> Password berikut hanya ditampilkan sekali. Simpan dengan aman.
                            </p>
                            <button type="button" @click="showPasswords = !showPasswords" class="text-sm text-yellow-700 underline hover:text-yellow-900 focus:outline-none">
                                <span x-text="showPasswords ? 'Sembunyikan Password' : 'Tampilkan Password'"></span>
                            </button>
                        </div>
                        <div x-show="showPasswords" x-transition class="bg-white border border-yellow-300 rounded-lg p-3 text-xs space-y-1 max-h-60 overflow-y-auto font-mono">
                            @foreach(session('generated_passwords') as $item)
                                <div class="flex justify-between border-b border-yellow-100 last:border-0 py-1">
                                    <span class="text-gray-700">{{ $item['name'] }} ({{ $item['nim'] }})</span>
                                    <span class="text-red-700 font-bold ml-4 break-all">{{ $item['password'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @role('staff')
        <div class="lg:hidden"><x-bottom-nav /></div>
    @endrole

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

            @if(session('generated_passwords'))
                <div x-data="{ showPasswords: false }" class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl" x-init="showPasswords = true">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-yellow-800 font-medium">
                            <strong>Perhatian:</strong> Password berikut hanya ditampilkan sekali. Simpan dengan aman.
                        </p>
                        <button type="button" @click="showPasswords = !showPasswords" class="text-sm text-yellow-700 underline hover:text-yellow-900 focus:outline-none">
                            <span x-text="showPasswords ? 'Sembunyikan Password' : 'Tampilkan Password'"></span>
                        </button>
                    </div>
                    <div x-show="showPasswords" x-transition class="bg-white border border-yellow-300 rounded-lg p-3 text-xs space-y-1 max-h-60 overflow-y-auto font-mono">
                        @foreach(session('generated_passwords') as $item)
                            <div class="flex justify-between border-b border-yellow-100 last:border-0 py-1">
                                <span class="text-gray-700">{{ $item['name'] }} ({{ $item['nim'] }})</span>
                                <span class="text-red-700 font-bold ml-4 break-all">{{ $item['password'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
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
