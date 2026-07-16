<x-app-layout>
    <div class="max-w-7xl mx-auto">

        <x-page-header title="Dashboard Inventaris" subtitle="Pantau stok keluar dan sisa stok terkini">
            <x-slot name="breadcrumb">
                <span class="text-gray-800 font-medium">Dashboard</span>
            </x-slot>
        </x-page-header>

        {{-- Stat Cards --}}
        <div x-data="{
            loading: true,
            stats: { totalItems: 0, totalStockIn: 0, totalStockOut: 0, criticalItems: 0 },
            init() {
                axios.get('{{ route('dashboard.stats') }}').then(res => {
                    this.stats = res.data;
                    this.loading = false;
                });
            }
        }">
            <div x-show="!loading" x-cloak class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <x-stat-card title="Total Item" value="0" color="primary" xValue="stats.totalItems" iconPath="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                <x-stat-card title="Stok Masuk" value="0" color="blue" xValue="stats.totalStockIn" iconPath="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                <x-stat-card title="Stok Keluar" value="0" color="amber" xValue="stats.totalStockOut" iconPath="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                <x-stat-card title="Item Kritis" value="0" color="red" xValue="stats.criticalItems" iconPath="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </div>
            <div x-show="loading" x-cloak class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <x-stat-card title="Total Item" value="0" color="primary" loading />
                <x-stat-card title="Stok Masuk" value="0" color="blue" loading />
                <x-stat-card title="Stok Keluar" value="0" color="amber" loading />
                <x-stat-card title="Item Kritis" value="0" color="red" loading />
            </div>
        </div>

        {{-- Stok Keluar --}}
        <div class="mb-8">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Stok Keluar</h3>

            <div x-data="serverTable('{{ route('dashboard.stock-out') }}')">
                <div class="mb-3">
                    <input type="text"
                           x-model="search"
                           @input.debounce.300ms="page=1; fetchData()"
                           placeholder="Cari barang..."
                           class="w-full lg:w-72 px-3 py-2 text-sm bg-white border border-gray-200 rounded-lg
                                  text-gray-800 placeholder-gray-400
                                  focus:border-primary-500 focus:ring-2 focus:ring-primary-100
                                  transition-colors">
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Barang</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody x-html="tableHtml" class="divide-y divide-gray-100">
                                @include('dashboards._stock-out-table')
                            </tbody>
                        </table>
                    </div>
                    <div x-html="paginationHtml" class="px-5 py-3 border-t border-gray-100">
                        @component('components.alpine-pagination', ['paginator' => $stockOuts])@endcomponent
                    </div>
                </div>
            </div>
        </div>

        {{-- Sisa Stok Per Item --}}
        <div class="mb-8">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Sisa Stok Per Item</h3>

            <div x-data="serverTable('{{ route('dashboard.stock-balance') }}')">
                <div class="mb-3">
                    <input type="text"
                           x-model="search"
                           @input.debounce.300ms="page=1; fetchData()"
                           placeholder="Cari barang..."
                           class="w-full lg:w-72 px-3 py-2 text-sm bg-white border border-gray-200 rounded-lg
                                  text-gray-800 placeholder-gray-400
                                  focus:border-primary-500 focus:ring-2 focus:ring-primary-100
                                  transition-colors">
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Barang</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                                </tr>
                            </thead>
                            <tbody x-html="tableHtml" class="divide-y divide-gray-100">
                                @include('dashboards._stock-balance-table')
                            </tbody>
                        </table>
                    </div>
                    <div x-html="paginationHtml" class="px-5 py-3 border-t border-gray-100">
                        @component('components.alpine-pagination', ['paginator' => $stockBalances])@endcomponent
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
