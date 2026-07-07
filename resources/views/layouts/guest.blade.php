<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Horizon UniStock') }}</title>
    <meta name="description" content="Login to Horizon UniStock — Student Uniform Distribution System">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

<div class="min-h-screen lg:flex">

    {{-- ===== LEFT: Branding (Desktop only) ===== --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 bg-primary-700 flex-col items-center justify-center relative overflow-hidden">

        {{-- Background pattern --}}
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="400" height="400" fill="url(#grid)"/>
            </svg>
        </div>

        {{-- Decorative circles --}}
        <div class="absolute top-[-80px] right-[-80px] w-72 h-72 bg-primary-600 rounded-full opacity-40"></div>
        <div class="absolute bottom-[-60px] left-[-60px] w-56 h-56 bg-primary-800 rounded-full opacity-50"></div>

        {{-- Content --}}
        <div class="relative z-10 text-center px-12 max-w-md">
            {{-- Logo icon --}}
            <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-8 border border-white/30">
                <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-white mb-3">Horizon UniStock</h1>
            <p class="text-primary-200 text-base leading-relaxed mb-10">
                Uniform distribution & inventory management system for new students
            </p>

            {{-- Flow steps --}}
            <div class="space-y-4 text-left">
                @foreach([
                    ['step' => '1', 'label' => 'Input Uniform Size'],
                    ['step' => '2', 'label' => 'Generate Identity QR'],
                    ['step' => '3', 'label' => 'Scan & Verify'],
                    ['step' => '4', 'label' => 'Receive Uniform'],
                ] as $item)
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 bg-white/20 border border-white/30 rounded-full flex items-center justify-center flex-shrink-0 text-white text-sm font-bold">
                        {{ $item['step'] }}
                    </div>
                    <span class="text-primary-100 text-sm">{{ $item['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== RIGHT: Form ===== --}}
    <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-gray-50">
        <div class="w-full max-w-md">

            {{-- Mobile: Logo --}}
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <div class="w-10 h-10 bg-primary-700 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Horizon UniStock</p>
                    <p class="text-xs text-primary-700">Uniform Distribution System</p>
                </div>
            </div>

            {{-- Form card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <p class="mt-6 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Horizon UniStock. All rights reserved.
            </p>
        </div>
    </div>

</div>

</body>
</html>
