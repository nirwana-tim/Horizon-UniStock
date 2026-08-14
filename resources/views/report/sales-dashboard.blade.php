<x-app-layout>
    <x-page-header title="Sales & Stock Analytics" subtitle="Dashboard analitik penjualan dan ketersediaan stok barang">
        <x-slot name="breadcrumb">
            <a href="{{ route('report.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">Reports</a>
            <span class="text-gray-300 mx-2">/</span>
            <span class="text-gray-800 font-medium">Sales Dashboard</span>
        </x-slot>
    </x-page-header>

    <div x-data="salesDashboard" class="space-y-6">
        {{-- Section 1: Interactive Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filter Analitik
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Start Date</label>
                    <input type="date" x-model="startDate" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">End Date</label>
                    <input type="date" x-model="endDate" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Item Group</label>
                    <select x-model="categoryId" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->label }} ({{ $category->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Item Selected</label>
                    <select x-model="itemId" :disabled="!categoryId" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500 disabled:bg-gray-50 disabled:text-gray-400">
                        <option value="">Semua Barang</option>
                        <template x-for="item in items" :key="item.id">
                            <option :value="item.id" x-text="item.name"></option>
                        </template>
                    </select>
                    <span x-show="!categoryId" class="text-[10px] text-gray-400 mt-1 block italic">*choose the item group first</span>
                </div>
            </div>
        </div>

        {{-- Section 2: KPI Stat Strip --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-4 py-2.5 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                </svg>
                <h3 class="text-xs font-semibold text-gray-800 uppercase tracking-wider">Ringkasan Kategori</h3>
            </div>
            <div class="flex overflow-x-auto divide-x divide-gray-100">
                <div class="sticky left-0 z-10 flex-shrink-0 px-4 py-3 min-w-[110px] bg-primary-50 border-r border-gray-200 shadow-[4px_0_10px_-4px_rgba(0,0,0,0.08)]">
                    <div class="text-[11px] font-semibold text-primary-800 uppercase tracking-wide mb-1">Grand Total</div>
                    <div class="text-lg font-bold text-primary-900 leading-tight" x-text="kpis.grand_total ? kpis.grand_total.sold.toLocaleString('id-ID') : 0">0</div>
                    <div class="text-[10px] text-primary-700">
                        Stok: <span class="font-medium" x-text="kpis.grand_total ? kpis.grand_total.stock.toLocaleString('id-ID') : 0">0</span>
                    </div>
                </div>
                @foreach($categories as $cat)
                    @php $code = strtolower($cat->code); @endphp
                    <div class="flex-shrink-0 px-4 py-3 min-w-[96px] hover:bg-gray-50 transition-colors">
                        <div class="text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1">{{ $cat->code }}</div>
                        <div class="text-lg font-bold text-primary-700 leading-tight" x-text="kpis['{{ $code }}'] ? kpis['{{ $code }}'].sold.toLocaleString('id-ID') : 0">0</div>
                        <div class="text-[10px] text-gray-400">
                            Stok: <span class="font-medium text-gray-500" x-text="kpis['{{ $code }}'] ? kpis['{{ $code }}'].stock.toLocaleString('id-ID') : 0">0</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Section 2.5: Stock Alerts (Demand vs Supply) --}}
        @if($shortageItems->isNotEmpty() || $outOfStockItems->isNotEmpty())
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-2.5 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="text-xs font-semibold text-gray-800 uppercase tracking-wider">Peringatan Stok (Berdasarkan Demand)</h3>
                    <x-badge type="danger">{{ $outOfStockItems->count() }} habis</x-badge>
                    <x-badge type="warning">{{ $shortageItems->count() }} kurang</x-badge>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Demand</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Kurang</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($outOfStockItems as $balance)
                                @php
                                    $demand = $balance->demand;
                                @endphp
                                @if($demand > 0)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-800">{{ $balance->item->name }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ $balance->variant?->size_label ?? '-' }}</td>
                                        <td class="px-4 py-2 text-sm font-mono text-gray-500">{{ $balance->variant?->sku ?? '-' }}</td>
                                        <td class="px-4 py-2 text-sm text-right text-gray-700">{{ number_format($demand) }}</td>
                                        <td class="px-4 py-2 text-sm text-right font-semibold text-red-600">{{ number_format($balance->quantity) }}</td>
                                        <td class="px-4 py-2 text-sm text-right font-semibold text-red-600">{{ number_format($demand) }}</td>
                                        <td class="px-4 py-2"><x-badge type="danger">Habis</x-badge></td>
                                    </tr>
                                @endif
                            @endforeach
                            @foreach($shortageItems as $row)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-800">{{ $row->item_name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $row->size_label }}</td>
                                    <td class="px-4 py-2 text-sm font-mono text-gray-500">{{ $row->sku }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-gray-700">{{ number_format($row->demand) }}</td>
                                    <td class="px-4 py-2 text-sm text-right font-semibold text-amber-600">{{ number_format($row->stock) }}</td>
                                    <td class="px-4 py-2 text-sm text-right font-semibold text-red-600">{{ number_format($row->shortage) }}</td>
                                    <td class="px-4 py-2"><x-badge type="warning">Kurang {{ $row->shortage }}</x-badge></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Section 3: Visualisasi Grafik --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Chart 1: Unit Sold by Items -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800">Unit Sold by Items</h4>
                    <span class="text-[10px] text-gray-400">Total unit barang yang terdistribusikan</span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="c1Chart"></canvas>
                </div>
            </div>

            <!-- Chart 2: Revenue by Items -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800">Revenue by Items</h4>
                    <span class="text-[10px] text-gray-400">Total nominal penjualan dalam Rupiah</span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="c2Chart"></canvas>
                </div>
            </div>

            <!-- Chart 3: Total Revenue and Unit Sold by Month -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800">Total Revenue and Unit Sold by Month</h4>
                    <span class="text-[10px] text-gray-400">Tren penjualan bulanan (Combo Chart)</span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="c3Chart"></canvas>
                </div>
            </div>

            <!-- Chart 4: Available Stock -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800">Available Stock</h4>
                    <span class="text-[10px] text-gray-400">Jumlah fisik stok yang tersedia saat ini</span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="c4Chart"></canvas>
                </div>
            </div>

            <!-- Chart 5: Value Stock -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800">Value Stock</h4>
                    <span class="text-[10px] text-gray-400">Nilai valuasi stok (Stok x HPP)</span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="c5Chart"></canvas>
                </div>
            </div>

            <!-- Chart 6: % Unit Sold -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800">% Unit Sold</h4>
                    <span class="text-[10px] text-gray-400">Kontribusi penjualan per item (%)</span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="c6Chart"></canvas>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
