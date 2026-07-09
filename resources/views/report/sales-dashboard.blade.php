<x-app-layout>
    <x-page-header title="Sales Dashboard" subtitle="Ringkasan penjualan per periode">
        <x-slot name="breadcrumb">
            <a href="{{ route('report.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">Reports</a>
            <span class="text-gray-300 mx-2">/</span>
            <span class="text-gray-800 font-medium">Sales Dashboard</span>
        </x-slot>
    </x-page-header>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <form method="GET" action="{{ route('report.sales-dashboard') }}" class="flex items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                <select name="month" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                    @foreach($months as $val => $label)
                        <option value="{{ $val }}" {{ (int) $month === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                <select name="year" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                    @foreach($years as $val)
                        <option value="{{ $val }}" {{ (int) $year === $val ? 'selected' : '' }}>{{ $val }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2 text-sm font-medium transition-colors">
                Tampilkan
            </button>
        </form>
    </div>

    {{-- Sesi 1: Category Summary --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Sales by Category</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @forelse($categories as $cat)
                <x-stat-card
                    title="{{ $cat->label }}"
                    value="{{ number_format($cat->unit_sold) }} / {{ number_format($cat->stock_avail) }}"
                    subtitle="Sold / Stock"
                    color="primary" />
            @empty
                <div class="col-span-full">
                    <x-empty-state title="Tidak ada data" description="Belum ada transaksi di periode ini" />
                </div>
            @endforelse
        </div>
    </div>

    {{-- Sesi 2: Revenue per Item --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-800">Revenue per Item</h3>
            <span class="text-xs text-gray-400">*Revenue dalam rupiah</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12">No</th>
                        <th class="py-3 px-4">Items</th>
                        <th class="py-3 px-4 text-right">Unit Sold</th>
                        <th class="py-3 px-4 text-right">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($revenueItems as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 text-gray-400">{{ $loop->iteration }}</td>
                            <td class="py-3 px-4 font-medium text-gray-800">{{ $item->name }}</td>
                            <td class="py-3 px-4 text-right text-gray-700">{{ number_format($item->unit_sold) }}</td>
                            <td class="py-3 px-4 text-right font-medium text-gray-800">Rp {{ number_format($item->revenue, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8">
                                <x-empty-state title="Tidak ada data" description="Belum ada transaksi di periode ini" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Sesi 3: Monthly Recap --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <x-stat-card
            title="Unit Sold"
            value="{{ number_format($monthlyRecap->unit_sold ?? 0) }}"
            subtitle="{{ $months[$month] ?? '' }} {{ $year }}"
            color="primary" />
        <x-stat-card
            title="Total Revenue"
            value="Rp {{ number_format($monthlyRecap->total_revenue ?? 0, 0, ',', '.') }}"
            subtitle=""
            color="success" />
    </div>

    {{-- Sesi 4: Stock vs Sold Detail --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Stock vs Sold Detail</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12">No</th>
                        <th class="py-3 px-4">Items</th>
                        <th class="py-3 px-4 text-right">Stock</th>
                        <th class="py-3 px-4 text-right">Value (Rp)</th>
                        <th class="py-3 px-4 text-center">% Sold</th>
                        <th class="py-3 px-4 text-right">Receive</th>
                        <th class="py-3 px-4 text-right">Sold</th>
                        <th class="py-3 px-4 text-right">Expense (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stockDetails as $item)
                        @php
                            $pctClass = $item->pct_sold > 95 ? 'danger' : ($item->pct_sold > 80 ? 'warning' : 'success');
                            $overSold = $item->available_stock < 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ $overSold ? 'bg-red-50' : '' }}">
                            <td class="py-3 px-4 text-gray-400">{{ $loop->iteration }}</td>
                            <td class="py-3 px-4 font-medium text-gray-800">{{ $item->name }}</td>
                            <td class="py-3 px-4 text-right {{ $overSold ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                {{ number_format($item->available_stock) }}
                            </td>
                            <td class="py-3 px-4 text-right text-gray-700">Rp {{ number_format($item->stock_value, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <x-badge type="{{ $pctClass }}">{{ number_format($item->pct_sold, 2) }}%</x-badge>
                            </td>
                            <td class="py-3 px-4 text-right text-gray-700">{{ number_format($item->stock_receive) }}</td>
                            <td class="py-3 px-4 text-right text-gray-700">{{ number_format($item->sum_sold) }}</td>
                            <td class="py-3 px-4 text-right text-gray-700">Rp {{ number_format($item->expense, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8">
                                <x-empty-state title="Tidak ada data" description="Belum ada transaksi di periode ini" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
