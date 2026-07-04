<x-app-layout>

    <x-page-header title="Dashboard Super Admin" subtitle="Monitor semua aktivitas dan sistem Horizon UniStock">
        <x-slot name="breadcrumb">
            <span class="text-gray-800 font-medium">Dashboard</span>
        </x-slot>
    </x-page-header>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">

        <x-stat-card
            title="Total User"
            :value="$totalUsers"
            color="primary"
            icon='<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>' />

        <x-stat-card
            title="Total Mahasiswa"
            :value="$totalStudents"
            color="blue"
            icon='<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>' />

        <x-stat-card
            title="Total Item"
            :value="$totalItems"
            color="primary"
            icon='<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>' />

        <x-stat-card
            title="Penerimaan Barang"
            :value="$totalStockReceives"
            color="green"
            icon='<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>' />

        <x-stat-card
            title="Stok Habis"
            :value="$outOfStockItems"
            :color="$outOfStockItems > 0 ? 'red' : 'green'"
            :trend="$outOfStockItems > 0 ? 'Perlu restok segera' : 'Stok aman'"
            icon='<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>' />

    </div>

    {{-- Low Stock Alert --}}
    @if($lowStockItems->count())
    <div class="bg-white rounded-xl border border-amber-200 shadow-sm mb-6 overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-3.5 border-b border-amber-100 bg-amber-50">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <h3 class="text-sm font-semibold text-amber-800">Peringatan Stok Menipis</h3>
                <p class="text-xs text-amber-600">{{ $lowStockItems->count() }} item perlu perhatian</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Sisa Stok</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($lowStockItems as $balance)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 text-gray-800 font-medium">{{ $balance->item?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-right font-bold {{ $balance->quantity <= 2 ? 'text-red-600' : 'text-amber-600' }}">
                            {{ $balance->quantity }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if($balance->quantity <= 2)
                                <x-badge type="danger">Kritis</x-badge>
                            @else
                                <x-badge type="warning">Menipis</x-badge>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Menu Cepat</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach([
                ['href' => route('master.faculty.index'),     'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'label' => 'Master Data'],
                ['href' => route('import.index'),             'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10', 'label' => 'Import'],
                ['href' => route('admin.stock-opname.index'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'label' => 'Stock Opname'],
                ['href' => route('reports.index'),            'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Reports'],
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

</x-app-layout>
