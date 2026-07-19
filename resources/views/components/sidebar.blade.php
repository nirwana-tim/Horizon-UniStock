{{--
  Sidebar Component — Admin, Finance & Super Admin
  Used in layouts/app.blade.php
--}}
@php
    $masterDataRoutes = [
        'master-data.faculty.*',
        'master-data.study-program.*',
        'master-data.program-level.*',
        'master-data.item-category.*',
        'master-data.item-type.*',
        'master-data.item-department.*',
        'master-data.item-size.*',
        'master-data.item.*',
        'master-data.item-price.*',
        'master-data.vendor.*',
    ];
    $studentsRoutes = ['students.*'];
    $distributionRoutes = [
        'distribution.entitlement.*',
        'distribution.distribution-schedule.*',
        'distribution.scan.*',
        'distribution.size-monitor.*',
        'distribution.stages.*',
    ];
    $inventoryRoutes = ['inventory.stock-receive.*', 'inventory.stock-opname.*', 'inventory.stock-balance.*', 'inventory.stock-movement.*'];
    $reportsRoutes = ['report.gpm-cost', 'report.gpm-cost.*', 'report.*'];
    $systemRoutes = ['admin.user.*', 'admin.audit-log.*', 'admin.system-config.*'];

    $masterOpen = request()->routeIs($masterDataRoutes) ? 'true' : 'false';
    $distributionOpen = request()->routeIs($distributionRoutes) ? 'true' : 'false';
    $inventoryOpen = request()->routeIs($inventoryRoutes) ? 'true' : 'false';
    $reportsOpen = request()->routeIs($reportsRoutes) ? 'true' : 'false';
    $systemOpen = request()->routeIs($systemRoutes) ? 'true' : 'false';
@endphp
<aside x-data="{ collapsed: false, mobileOpen: false, userMenuOpen: false, masterOpen: {{ $masterOpen }}, distributionOpen: {{ $distributionOpen }}, inventoryOpen: {{ $inventoryOpen }}, reportsOpen: {{ $reportsOpen }}, systemOpen: {{ $systemOpen }} }" x-init="setTimeout(() => $el.classList.add('sidebar-transition'), 50)" @toggle-sidebar.window="mobileOpen = !mobileOpen"
    class="fixed inset-y-0 left-0 z-30 flex-shrink-0 bg-white border-r border-gray-200 flex flex-col h-full overflow-hidden lg:overflow-y-auto custom-scroll transition-transform duration-250 lg:transform-none lg:static lg:z-auto"
    :class="{
        'w-16': collapsed,
        'w-64': !collapsed,
        'translate-x-0': mobileOpen,
        '-translate-x-full': !mobileOpen
    }">

    {{-- Logo / Brand --}}
    <div class="h-14 border-b border-gray-100 flex-shrink-0 flex items-center overflow-hidden"
        :class="collapsed ? 'justify-center px-0' : 'justify-between px-4'">

        {{-- Expanded: Logo + text --}}
        <a x-show="!collapsed" href="{{ route('dashboard') }}" class="flex items-center gap-2 min-w-0">
            <div class="w-8 h-8 bg-primary-700 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg aria-hidden="true" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-bold text-gray-900 leading-tight truncate">UniStock</p>
            </div>
        </a>

        {{-- Expanded: Collapse button --}}
        <button x-show="!collapsed" x-cloak @click="collapsed = true" title="Collapse sidebar" aria-label="Ciutkan sidebar"
            class="p-1.5 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors flex-shrink-0">
            <svg aria-hidden="true" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>

        {{-- Collapsed: Single centered expand button --}}
        <button x-show="collapsed" x-cloak @click="collapsed = false" title="Expand sidebar" aria-label="Perluas sidebar"
            class="w-10 h-10 flex items-center justify-center rounded-lg text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-hidden py-3 px-2 space-y-0.5">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" title="Dashboard"
            class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'sidebar-item-active' : 'sidebar-item' }}">
            <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span x-show="!collapsed" class="truncate">Dashboard</span>
        </a>

        {{-- ===== ADMIN / FINANCE MENU ===== --}}
        @hasanyrole(['admin', 'staff', 'super_admin'])
            {{-- Master Data (Collapsible) --}}
            <div>
                <button @click="collapsed ? (collapsed=false, masterOpen=true) : masterOpen = !masterOpen"
                    class="w-full flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs($masterDataRoutes) ? 'sidebar-item-active' : 'sidebar-item' }}" title="Master Data">
                    <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                    </svg>
                    <span x-show="!collapsed" class="flex-1 text-left truncate">Master Data</span>
                    <svg aria-hidden="true" x-show="!collapsed" :class="masterOpen ? 'rotate-180' : ''"
                        class="w-4 h-4 flex-shrink-0 transition-transform duration-200" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="masterOpen && !collapsed" x-cloak
                    class="mt-0.5 ml-4 pl-4 border-l border-gray-200 space-y-0.5">
                    {{-- Institution --}}
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 py-1.5 mt-1">Institution</p>
                    <a href="{{ route('master-data.faculty.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master-data.faculty.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Faculty
                    </a>
                    <a href="{{ route('master-data.study-program.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master-data.study-program.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Study Program
                    </a>
                    <a href="{{ route('master-data.program-level.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master-data.program-level.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Program Level
                    </a>

                    {{-- Item --}}
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 py-1.5 mt-2">Item</p>
                    <a href="{{ route('master-data.item-category.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master-data.item-category.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Item Category
                    </a>
                    <a href="{{ route('master-data.item-type.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master-data.item-type.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Item Type
                    </a>
                    <a href="{{ route('master-data.item-department.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master-data.item-department.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Item Department
                    </a>
                    <a href="{{ route('master-data.item-size.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master-data.item-size.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Item Size
                    </a>
                    <a href="{{ route('master-data.item.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master-data.item.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Item / SKU
                    </a>
                    <a href="{{ route('master-data.item-price.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master-data.item-price.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Item Price
                    </a>

                    {{-- Other --}}
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 py-1.5 mt-2">Other</p>
                    <a href="{{ route('master-data.vendor.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('master-data.vendor.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Vendor
                    </a>
                </div>
            </div>

            {{-- Import Data --}}
            <a href="{{ route('import.index') }}" title="Import Data"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('import.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
                <span x-show="!collapsed" class="truncate">Import Data</span>
            </a>

            {{-- Eligibility --}}
            <a href="{{ route('finance.eligibility.index') }}" title="Student Eligibility"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('finance.eligibility.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-show="!collapsed" class="truncate">Student Eligibility</span>
            </a>

            {{-- Students --}}
            <a href="{{ route('students.index') }}" title="Students"
                class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs('students.*') ? 'sidebar-item-active' : 'sidebar-item' }}">
                <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span x-show="!collapsed" class="truncate">Students</span>
            </a>

            {{-- Distribution (Collapsible) --}}
            <div>
                <button
                    @click="collapsed ? (collapsed=false, distributionOpen=true) : distributionOpen = !distributionOpen"
                    class="w-full flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs($distributionRoutes) ? 'sidebar-item-active' : 'sidebar-item' }}" title="Distribution">
                    <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="!collapsed" class="flex-1 text-left truncate">Distribution</span>
                    <svg aria-hidden="true" x-show="!collapsed" :class="distributionOpen ? 'rotate-180' : ''"
                        class="w-4 h-4 flex-shrink-0 transition-transform duration-200" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="distributionOpen && !collapsed" x-cloak
                    class="mt-0.5 ml-4 pl-4 border-l border-gray-200 space-y-0.5">
                    <a href="{{ route('distribution.entitlement.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('distribution.entitlement.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Entitlement
                    </a>
                    <a href="{{ route('distribution.distribution-schedule.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('distribution.distribution-schedule.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Distribution Schedule
                    </a>
                    <a href="{{ route('distribution.scan.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('distribution.scan.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Scan & Distribution
                    </a>
                    <a href="{{ route('distribution.size-monitor.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('distribution.size-monitor.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Size Monitor
                    </a>
                    <a href="{{ route('distribution.stages.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('distribution.stages.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Stages
                    </a>
                </div>
            </div>

            {{-- Inventory (Collapsible) --}}
            <div>
                <button @click="collapsed ? (collapsed=false, inventoryOpen=true) : inventoryOpen = !inventoryOpen"
                    class="w-full flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs($inventoryRoutes) ? 'sidebar-item-active' : 'sidebar-item' }}" title="Inventory">
                    <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <span x-show="!collapsed" class="flex-1 text-left truncate">Inventory</span>
                    <svg aria-hidden="true" x-show="!collapsed" :class="inventoryOpen ? 'rotate-180' : ''"
                        class="w-4 h-4 flex-shrink-0 transition-transform duration-200" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="inventoryOpen && !collapsed" x-cloak
                    class="mt-0.5 ml-4 pl-4 border-l border-gray-200 space-y-0.5">
                    <a href="{{ route('inventory.stock-receive.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('inventory.stock-receive.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Stock Receive
                    </a>
                    <a href="{{ route('inventory.stock-balance.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('inventory.stock-balance.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Stock Balance
                    </a>
                    <a href="{{ route('inventory.stock-movement.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('inventory.stock-movement.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Stock Movement
                    </a>
                    <a href="{{ route('inventory.stock-opname.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('inventory.stock-opname.*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Stock Opname
                    </a>
                </div>
            </div>

            {{-- Reports (Collapsible) --}}
            <div>
                <button @click="collapsed ? (collapsed=false, reportsOpen=true) : reportsOpen = !reportsOpen"
                    class="w-full flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs($reportsRoutes) ? 'sidebar-item-active' : 'sidebar-item' }}" title="Reports">
                    <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="!collapsed" class="flex-1 text-left truncate">Reports</span>
                    <svg aria-hidden="true" x-show="!collapsed" :class="reportsOpen ? 'rotate-180' : ''"
                        class="w-4 h-4 flex-shrink-0 transition-transform duration-200" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="reportsOpen && !collapsed" x-cloak
                    class="mt-0.5 ml-4 pl-4 border-l border-gray-200 space-y-0.5">
                    <a href="{{ route('report.gpm-cost') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('report.gpm-cost*') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        GPM / Cost
                    </a>
                    <a href="{{ route('report.index') }}"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm {{ request()->routeIs('report.index') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }} transition-colors">
                        Reports
                    </a>
                </div>
            </div>
        @endhasanyrole

        {{-- ===== SUPER ADMIN ONLY ===== --}}
        @role('super_admin')
            <div class="my-2 border-t border-gray-100"></div>

            {{-- System (Collapsible) --}}
            <div>
                <button @click="collapsed ? (collapsed=false, systemOpen=true) : systemOpen = !systemOpen"
                    class="w-full flex items-center gap-3 px-2 py-2 rounded-lg text-sm {{ request()->routeIs($systemRoutes) ? 'sidebar-item-active' : 'sidebar-item' }}" title="System">
                    <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span x-show="!collapsed" class="flex-1 text-left truncate">System</span>
                    <svg aria-hidden="true" x-show="!collapsed" :class="systemOpen ? 'rotate-180' : ''"
                        class="w-4 h-4 flex-shrink-0 transition-transform duration-200" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="systemOpen && !collapsed" x-cloak
                    class="mt-0.5 ml-4 pl-4 border-l border-gray-200 space-y-0.5">
                    <a href="#"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors">
                        <svg aria-hidden="true" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Users & Roles
                    </a>
                    <a href="#"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors">
                        <svg aria-hidden="true" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Audit Log
                    </a>
                    <a href="#"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors">
                        <svg aria-hidden="true" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        System Config
                    </a>
                </div>
            </div>
        @endrole

    </nav>

    {{-- Bottom: User Info --}}
    <div class="flex-shrink-0 border-t border-gray-100 p-3 bg-white sticky bottom-0 z-10">
        <div class="relative">
            <button @click="userMenuOpen = !userMenuOpen"
                class="w-full flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                {{-- Avatar --}}
                <div
                    class="w-8 h-8 bg-primary-700 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold uppercase">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div x-show="!collapsed" class="flex-1 text-left min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate capitalize">
                        {{ Auth::user()->getRoleNames()->first() ?? 'user' }}</p>
                </div>
                <svg aria-hidden="true" x-show="!collapsed" class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                </svg>
            </button>

            {{-- User Dropdown --}}
            <div x-show="userMenuOpen" @click.away="userMenuOpen = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                :class="collapsed ? 'fixed bottom-2 left-[72px] w-56' : 'absolute bottom-full left-0 right-0 mb-1'"
                class="bg-white rounded-lg border border-gray-200 shadow-lg py-1 z-50">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg aria-hidden="true" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    My Profile
                </a>
                <div class="border-t border-gray-100 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                        <svg aria-hidden="true" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

</aside>

{{-- Mobile backdrop --}}
<div x-data="{}" @toggle-sidebar.window="$el.classList.toggle('hidden')"
    @click="$dispatch('toggle-sidebar')" class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden cursor-pointer"></div>
