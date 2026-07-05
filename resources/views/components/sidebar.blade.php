{{--
  Sidebar Component — Admin & Super Admin only
  Digunakan di layouts/app.blade.php
--}}
@php
    $masterDataRoutes = [
        'master.faculty.*',
        'master.study-program.*',
        'master.program-level.*',
        'master.student.*',
        'master.item-category.*',
        'master.item-type.*',
        'master.item-department.*',
        'master.item-size.*',
        'master.item.*',
        'master.item-price.*',
        'master.vendor.*',
    ];
    $masterOpen = request()->routeIs($masterDataRoutes) ? 'true' : 'false';
@endphp

<!-- Mobile Backdrop -->
<div id="sidebar-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

<aside id="mobile-sidebar" x-data="{ collapsed: false, masterOpen: {{ $masterOpen }}, stockOpen: false }" x-init="setTimeout(() => $el.classList.add('sidebar-transition'), 50)" :class="collapsed ? 'lg:w-16' : 'w-64'"
    class="fixed inset-y-0 left-0 z-50 transform -translate-x-full transition-transform duration-300 lg:static lg:translate-x-0 lg:block flex-shrink-0 bg-white border-r border-gray-200 flex flex-col h-full overflow-hidden">

    {{-- Logo / Brand --}}
    <div class="h-14 border-b border-gray-100 flex-shrink-0 flex items-center overflow-hidden"
        :class="collapsed ? 'justify-center px-0' : 'justify-between px-4'">

        {{-- Expanded: Logo + text --}}
        <a x-show="!collapsed" href="{{ route('dashboard') }}" class="flex items-center gap-2 min-w-0">
            <div class="w-8 h-8 bg-primary-700 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-bold text-gray-900 leading-tight truncate">Horizon</p>
                <p class="text-xs font-medium text-primary-700 truncate">UniStock</p>
            </div>
        </a>

        {{-- Expanded: Collapse button --}}
        <button x-show="!collapsed" @click="collapsed = true" title="Tutup sidebar"
            class="hidden lg:block p-1.5 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>

        {{-- Collapsed: Single centered expand button --}}
        <button x-show="collapsed" @click="collapsed = false" title="Buka sidebar"
            class="hidden lg:flex w-10 h-10 items-center justify-center rounded-lg text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto custom-scroll py-3 px-2 space-y-0.5">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'sidebar-item-active' : 'sidebar-item' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span x-show="!collapsed" class="truncate">Dashboard</span>
        </a>

        {{-- ===== STUDENT MENU ===== --}}
        @role('student')
            {{-- Ukuran Saya --}}
            <a href="{{ route('student.sizes.index') }}"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('student.sizes.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
                <span x-show="!collapsed" class="truncate">Ukuran Saya</span>
            </a>

            {{-- QR Code Saya --}}
            <a href="{{ route('student.qr') }}"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('student.qr') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8H3a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2v-8a2 2 0 00-2-2h-2" />
                </svg>
                <span x-show="!collapsed" class="truncate">QR Code Saya</span>
            </a>
        @endrole

        {{-- ===== FINANCE / ADMIN MENU ===== --}}
        @hasanyrole(['admin', 'finance', 'super_admin'])

            {{-- Master Data (Collapsible) --}}
            <div>
                <button @click="masterOpen = !masterOpen"
                    class="w-full flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs($masterDataRoutes) ? 'sidebar-item-active' : 'sidebar-item' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                    </svg>
                    <span x-show="!collapsed" class="flex-1 text-left truncate">Master Data</span>
                    <svg x-show="!collapsed" :class="masterOpen ? 'rotate-180' : ''"
                        class="w-4 h-4 flex-shrink-0 transition-transform duration-200" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="masterOpen && !collapsed" x-collapse
                    class="mt-0.5 ml-4 pl-4 border-l border-gray-200 space-y-0.5">
                    {{-- Institusi --}}
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 py-1.5 mt-1">Institusi</p>
                    <a href="{{ route('master.faculty.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master.faculty.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Fakultas
                    </a>
                    <a href="{{ route('master.study-program.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master.study-program.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Program Studi
                    </a>
                    <a href="{{ route('master.program-level.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master.program-level.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Level Program
                    </a>

                    {{-- Mahasiswa --}}
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 py-1.5 mt-2">Mahasiswa</p>
                    <a href="{{ route('master.student.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master.student.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Data Mahasiswa
                    </a>

                    {{-- Item --}}
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 py-1.5 mt-2">Item</p>
                    <a href="{{ route('master.item-category.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master.item-category.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Kategori Item
                    </a>
                    <a href="{{ route('master.item-type.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master.item-type.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Tipe Item
                    </a>
                    <a href="{{ route('master.item-department.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master.item-department.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Departemen Item
                    </a>
                    <a href="{{ route('master.item-size.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master.item-size.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Ukuran Item
                    </a>
                    <a href="{{ route('master.item.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master.item.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Item / SKU
                    </a>
                    <a href="{{ route('master.item-price.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master.item-price.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Harga Item
                    </a>

                    {{-- Lainnya --}}
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 py-1.5 mt-2">Lainnya</p>
                    <a href="{{ route('master.vendor.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master.vendor.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Vendor
                    </a>
                </div>
            </div>

            {{-- Import Data --}}
            <a href="{{ route('import.index') }}"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('import.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
                <span x-show="!collapsed" class="truncate">Import Data</span>
            </a>

            {{-- Entitlement --}}
            <a href="{{ route('master.entitlement.index') }}"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('master.entitlement.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span x-show="!collapsed" class="truncate">Entitlement</span>
            </a>

            {{-- Jadwal Distribusi --}}
            <a href="{{ route('master.distribution-schedule.index') }}"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('master.distribution-schedule.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span x-show="!collapsed" class="truncate">Jadwal Distribusi</span>
            </a>

            {{-- Penerimaan Stok --}}
            <a href="{{ route('master.stock-receive.index') }}"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('master.stock-receive.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <span x-show="!collapsed" class="truncate">Penerimaan Stok</span>
            </a>

            {{-- Generate Akun --}}
            <a href="{{ route('master.student-account.index') }}"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('master.student-account.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span x-show="!collapsed" class="truncate">Generate Akun</span>
            </a>

            {{-- Monitor Ukuran --}}
            <a href="{{ route('master.size-monitor.index') }}"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('master.size-monitor.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span x-show="!collapsed" class="truncate">Monitor Ukuran</span>
            </a>

            {{-- Scan & Distribusi (Finance + Staff access) --}}
            @hasanyrole(['admin', 'finance', 'staff'])
                <a href="{{ route('staff.scan.index') }}"
                    class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('staff.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8H3a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2v-8a2 2 0 00-2-2h-2" />
                    </svg>
                    <span x-show="!collapsed" class="truncate">Scan & Distribusi</span>
                </a>
            @endhasanyrole

            {{-- Divider --}}
            <div class="my-2 border-t border-gray-100"></div>

            {{-- Stock Opname --}}
            <a href="{{ route('admin.stock-opname.index') }}"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('admin.stock-opname.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span x-show="!collapsed" class="truncate">Stock Opname</span>
            </a>

            {{-- GPM / Cost --}}
            <a href="{{ route('admin.gpm.index') }}"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('admin.gpm.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span x-show="!collapsed" class="truncate">GPM / Cost</span>
            </a>

            {{-- Reports --}}
            <a href="{{ route('reports.index') }}"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('reports.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span x-show="!collapsed" class="truncate">Reports</span>
            </a>

        @endhasanyrole

        {{-- ===== SUPER ADMIN ONLY ===== --}}
        @role('super_admin')
            <div class="my-2 border-t border-gray-100"></div>
            <p x-show="!collapsed" class="px-2 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Sistem
            </p>

            <a href="#" class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm sidebar-item">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span x-show="!collapsed" class="truncate">Pengguna & Role</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm sidebar-item">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span x-show="!collapsed" class="truncate">Audit Log</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm sidebar-item">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span x-show="!collapsed" class="truncate">System Config</span>
            </a>
        @endrole

    </nav>

    {{-- Bottom: User Info --}}
    <div class="flex-shrink-0 border-t border-gray-100 p-3">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="w-full flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                {{-- Avatar --}}
                <div
                    class="w-8 h-8 bg-primary-700 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold uppercase">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div x-show="!collapsed" class="flex-1 text-left min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate capitalize">
                        {{ Auth::user()->getRoleNames()->first() ?? 'user' }}</p>
                </div>
                <svg x-show="!collapsed" class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                </svg>
            </button>

            {{-- User Dropdown --}}
            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                class="absolute bottom-full left-0 right-0 mb-1 bg-white rounded-lg border border-gray-200 shadow-lg py-1 z-50">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profil Saya
                </a>
                <div class="border-t border-gray-100 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

</aside>

<!-- Script JS sederhana untuk toggle class -->
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('mobile-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        
        if (sidebar && backdrop) {
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }
    }

    // Listen to toggle-sidebar event dispatched from Topbar
    document.addEventListener('toggle-sidebar', toggleSidebar);
</script>
