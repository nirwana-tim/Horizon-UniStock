<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Horizon UniStock — Sistem Distribusi Seragam Mahasiswa</title>
    <meta name="description" content="Horizon UniStock adalah sistem distribusi seragam dan manajemen inventori berbasis web untuk mahasiswa baru universitas.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .hero-gradient { background: linear-gradient(135deg, #5c040e 0%, #980416 50%, #b80e20 100%); }
        .hero-pattern {
            background-image: radial-gradient(circle at 25px 25px, rgba(255,255,255,0.08) 2px, transparent 0);
            background-size: 50px 50px;
        }
        .glass-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .feature-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(152,4,22,0.08);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">

<!-- ===== NAVBAR ===== -->
<header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-200/60">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 bg-primary-700 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-900">Horizon <span class="text-primary-700">UniStock</span></span>
        </div>

        <!-- Nav links -->
        <nav class="hidden md:flex items-center gap-6">
            <a href="#alur" class="text-sm text-gray-600 hover:text-primary-700 transition-colors">Alur</a>
            <a href="#fitur" class="text-sm text-gray-600 hover:text-primary-700 transition-colors">Fitur</a>
        </nav>

        <!-- CTA -->
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}"
                   class="px-4 py-2 bg-primary-700 text-white text-sm font-medium rounded-lg hover:bg-primary-800 transition-colors">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="px-4 py-2 bg-primary-700 text-white text-sm font-medium rounded-lg hover:bg-primary-800 transition-colors">
                    Masuk
                </a>
            @endauth
        @endif
    </div>
</header>

<!-- ===== HERO ===== -->
<section class="hero-gradient hero-pattern pt-32 pb-24 px-6 relative overflow-hidden">

    <!-- Decorative elements -->
    <div class="absolute top-20 right-0 w-96 h-96 bg-primary-600/30 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-primary-900/40 rounded-full blur-3xl"></div>

    <div class="max-w-4xl mx-auto text-center relative z-10">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 border border-white/20 rounded-full text-xs text-primary-100 font-medium mb-6">
            <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
            Sistem Aktif — MVP Freshman 2025/2026
        </div>

        <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight mb-5">
            Distribusi Seragam<br>
            <span class="text-primary-200">Lebih Cepat & Terstruktur</span>
        </h1>
        <p class="text-primary-100 text-lg leading-relaxed mb-10 max-w-2xl mx-auto">
            Horizon UniStock menggantikan proses manual Google Form, barcode, dan rekap Excel
            dengan sistem terintegrasi dari input ukuran hingga laporan distribusi.
        </p>

        <!-- CTAs -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            @auth
                <a href="{{ url('/dashboard') }}"
                   class="w-full sm:w-auto px-8 py-3.5 bg-white text-primary-700 text-sm font-bold rounded-xl hover:bg-primary-50 transition-colors shadow-lg">
                    Buka Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="w-full sm:w-auto px-8 py-3.5 bg-white text-primary-700 text-sm font-bold rounded-xl hover:bg-primary-50 transition-colors shadow-lg">
                    Login Mahasiswa
                </a>
                <a href="{{ route('login') }}"
                   class="w-full sm:w-auto px-8 py-3.5 glass-card text-white text-sm font-medium rounded-xl hover:bg-white/15 transition-all">
                    Login Staff / Admin
                </a>
            @endauth
        </div>
    </div>

    <!-- Hero visual - stats strip -->
    <div class="max-w-3xl mx-auto mt-16 grid grid-cols-3 gap-4 relative z-10">
        @foreach([
            ['value' => '< 5 menit', 'label' => 'Per mahasiswa'],
            ['value' => 'Real-time', 'label' => 'Tracking stok'],
            ['value' => '4 Role', 'label' => 'Akses terkelola'],
        ] as $stat)
        <div class="glass-card rounded-xl p-5 text-center">
            <p class="text-xl font-bold text-white">{{ $stat['value'] }}</p>
            <p class="text-xs text-primary-200 mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

</section>

<!-- ===== ALUR ===== -->
<section id="alur" class="py-20 px-6 bg-white">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-14">
            <p class="text-xs font-semibold text-primary-700 uppercase tracking-widest mb-2">Alur Sistem</p>
            <h2 class="text-2xl font-bold text-gray-800">Dari Pendaftaran hingga Seragam di Tangan</h2>
            <p class="mt-2 text-sm text-gray-500 max-w-xl mx-auto">
                Proses yang sebelumnya membutuhkan berjam-jam kini diselesaikan dalam hitungan menit
            </p>
        </div>

        <div class="relative">
            <!-- Connector line (desktop) -->
            <div class="hidden md:block absolute top-10 left-[12.5%] right-[12.5%] h-0.5 bg-gradient-to-r from-primary-200 via-primary-500 to-primary-200"></div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                @foreach([
                    ['step' => '1', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'title' => 'Input Ukuran', 'desc' => 'Mahasiswa mengisi profil ukuran seragam & sepatu secara mandiri'],
                    ['step' => '2', 'icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8H3a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2v-8a2 2 0 00-2-2h-2', 'title' => 'Generate QR', 'desc' => 'Sistem auto-generate QR identitas permanen setelah data lengkap'],
                    ['step' => '3', 'icon' => 'M15 10l4.553-2.069A1 1 0 0121 8.816V15.18a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'title' => 'Scan & Verifikasi', 'desc' => 'Staff scan QR mahasiswa, verifikasi eligibility & checklist barang'],
                    ['step' => '4', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'title' => 'Terima Seragam', 'desc' => 'Transaksi tercatat, stok otomatis berkurang, laporan siap dieksport'],
                ] as $item)
                <div class="flex flex-col items-center text-center">
                    <!-- Icon circle -->
                    <div class="relative z-10 w-20 h-20 bg-primary-700 rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-primary-200">
                        <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $item['icon'] }}"/>
                        </svg>
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-primary-900 rounded-full flex items-center justify-center text-xs font-bold text-white">
                            {{ $item['step'] }}
                        </div>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800 mb-2">{{ $item['title'] }}</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">{{ $item['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- ===== FITUR ===== -->
<section id="fitur" class="py-20 px-6 bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-14">
            <p class="text-xs font-semibold text-primary-700 uppercase tracking-widest mb-2">Keunggulan</p>
            <h2 class="text-2xl font-bold text-gray-800">Mengapa Horizon UniStock?</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                [
                    'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                    'color' => 'primary',
                    'title' => 'Cepat & Efisien',
                    'desc' => 'Kurangi waktu distribusi dari >15 menit menjadi <5 menit per mahasiswa dengan sistem QR permanen.',
                ],
                [
                    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'color' => 'green',
                    'title' => 'Akurat & Anti Duplikat',
                    'desc' => 'Eliminasi risiko double submit, salah ukuran, dan data hilang. Sistem validasi berjalan real-time.',
                ],
                [
                    'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'color' => 'blue',
                    'title' => 'Laporan Terstruktur',
                    'desc' => 'Export laporan distribusi, stok, dan GPM dalam format Excel siap kirim kapan saja.',
                ],
            ] as $feat)
            @php
                $colorCls = [
                    'primary' => ['bg' => 'bg-primary-100', 'icon' => 'text-primary-700', 'border' => 'border-primary-100'],
                    'green'   => ['bg' => 'bg-green-100',   'icon' => 'text-green-700',   'border' => 'border-green-100'],
                    'blue'    => ['bg' => 'bg-blue-100',    'icon' => 'text-blue-700',     'border' => 'border-blue-100'],
                ][$feat['color']];
            @endphp
            <div class="feature-card bg-white rounded-2xl border border-gray-200 p-7">
                <div class="{{ $colorCls['bg'] }} w-12 h-12 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 {{ $colorCls['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feat['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-800 mb-2">{{ $feat['title'] }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $feat['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== CTA BOTTOM ===== -->
<section class="py-20 px-6 hero-gradient relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern opacity-50"></div>
    <div class="max-w-2xl mx-auto text-center relative z-10">
        <h2 class="text-2xl font-bold text-white mb-3">Siap Mulai?</h2>
        <p class="text-primary-200 text-sm mb-8">Masuk ke sistem sesuai peran Anda</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            @auth
                <a href="{{ url('/dashboard') }}"
                   class="w-full sm:w-auto px-8 py-3 bg-white text-primary-700 text-sm font-bold rounded-xl hover:bg-primary-50 transition-colors">
                    Buka Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="w-full sm:w-auto px-8 py-3 bg-white text-primary-700 text-sm font-bold rounded-xl hover:bg-primary-50 transition-colors">
                    Login Sekarang
                </a>
            @endauth
        </div>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="bg-primary-950 py-8 px-6">
    <div class="max-w-4xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 bg-primary-700 rounded flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-primary-200">Horizon UniStock</span>
        </div>
        <p class="text-xs text-primary-400">&copy; {{ date('Y') }} Sistem Distribusi Seragam Mahasiswa. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
