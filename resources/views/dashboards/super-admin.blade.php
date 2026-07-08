<x-app-layout>
    <div x-data="{
        stats: { totalUsers: 0, totalStudents: 0, totalItems: 0, totalStockReceives: 0, outOfStockItems: 0 },
        lowStockHtml: '',
        init() {
            axios.get('{{ route('dashboard.stats') }}').then(res => { this.stats = res.data; });
            axios.get('{{ route('dashboard.low-stock') }}').then(res => { this.lowStockHtml = res.data.html; });
        }
    }">

    <x-page-header title="Dashboard Super Admin" subtitle="Monitor semua aktivitas dan sistem Horizon UniStock">
        <x-slot name="breadcrumb">
            <span class="text-gray-800 font-medium">Dashboard</span>
        </x-slot>
    </x-page-header>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">

        <x-stat-card
            title="Total User"
            value="0"
            color="primary"
            xValue="stats.totalUsers"
            iconPath="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />

        <x-stat-card
            title="Total Students"
            value="0"
            color="blue"
            xValue="stats.totalStudents"
            iconPath="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />

        <x-stat-card
            title="Total Item"
            value="0"
            color="primary"
            xValue="stats.totalItems"
            iconPath="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />

        <x-stat-card
            title="Stock Receives"
            value="0"
            color="green"
            xValue="stats.totalStockReceives"
            iconPath="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />

        <x-stat-card
            title="Stok Habis"
            value="0"
            color="red"
            xValue="stats.outOfStockItems"
            iconPath="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />

    </div>

    {{-- Low Stock Alert --}}
    <div x-html="lowStockHtml"></div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Quick Menu</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach([
                ['href' => route('master-data.faculty.index'),     'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'label' => 'Master Data'],
                ['href' => route('import.index'),             'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10', 'label' => 'Import'],
                ['href' => route('inventory.stock-opname.index'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'label' => 'Stock Opname'],
                ['href' => route('report.index'),            'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Reports'],
            ] as $item)
            <a href="{{ $item['href'] }}"
               class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-200 hover:border-primary-200 hover:bg-primary-50 transition-all group">
                <div class="w-9 h-9 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-primary-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-600 group-hover:text-primary-700 text-center transition-colors">{{ $item['label'] }}</span>
            </a>
            @endforeach
        </div>
    </div>

    </div>
</x-app-layout>
