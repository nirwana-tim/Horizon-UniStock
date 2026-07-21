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

        {{-- Section 2: KPI Scorecards --}}
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Card 1: KTM -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 left-0 right-0 h-1 bg-red-600"></div>
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">KTM</span>
                        <span class="p-1 bg-red-50 text-red-700 rounded-md">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.33 0 4 1 4 2v1H5v-1c0-1 2.67-2 4-2z" />
                            </svg>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900" x-text="kpis.ktm ? kpis.ktm.sold.toLocaleString('id-ID') : 0">0</div>
                    <div class="text-xs text-gray-400 font-medium">Unit Sold</div>
                </div>
                <div class="mt-4 pt-2 border-t border-gray-100 flex justify-between items-center text-xs">
                    <span class="text-gray-500">Stock Avail:</span>
                    <span class="font-bold text-gray-800" x-text="kpis.ktm ? kpis.ktm.stock.toLocaleString('id-ID') : 0">0</span>
                </div>
            </div>

            <!-- Card 2: UNIFORM -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 left-0 right-0 h-1 bg-primary-700"></div>
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Uniform</span>
                        <span class="p-1 bg-red-50 text-primary-700 rounded-md">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900" x-text="kpis.uniform ? kpis.uniform.sold.toLocaleString('id-ID') : 0">0</div>
                    <div class="text-xs text-gray-400 font-medium">Unit Sold</div>
                </div>
                <div class="mt-4 pt-2 border-t border-gray-100 flex justify-between items-center text-xs">
                    <span class="text-gray-500">Stock Avail:</span>
                    <span class="font-bold text-gray-800" x-text="kpis.uniform ? kpis.uniform.stock.toLocaleString('id-ID') : 0">0</span>
                </div>
            </div>

            <!-- Card 3: SHOES -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 left-0 right-0 h-1 bg-emerald-600"></div>
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Shoes</span>
                        <span class="p-1 bg-emerald-50 text-emerald-700 rounded-md">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900" x-text="kpis.shoes ? kpis.shoes.sold.toLocaleString('id-ID') : 0">0</div>
                    <div class="text-xs text-gray-400 font-medium">Unit Sold</div>
                </div>
                <div class="mt-4 pt-2 border-t border-gray-100 flex justify-between items-center text-xs">
                    <span class="text-gray-500">Stock Avail:</span>
                    <span class="font-bold text-gray-800" x-text="kpis.shoes ? kpis.shoes.stock.toLocaleString('id-ID') : 0">0</span>
                </div>
            </div>

            <!-- Card 4: KIT -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 left-0 right-0 h-1 bg-amber-500"></div>
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kit</span>
                        <span class="p-1 bg-amber-50 text-amber-700 rounded-md">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                            </svg>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900" x-text="kpis.kit ? kpis.kit.sold.toLocaleString('id-ID') : 0">0</div>
                    <div class="text-xs text-gray-400 font-medium">Unit Sold</div>
                </div>
                <div class="mt-4 pt-2 border-t border-gray-100 flex justify-between items-center text-xs">
                    <span class="text-gray-500">Stock Avail:</span>
                    <span class="font-bold text-gray-800" x-text="kpis.kit ? kpis.kit.stock.toLocaleString('id-ID') : 0">0</span>
                </div>
            </div>

            <!-- Card 5: TUMBLER -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 left-0 right-0 h-1 bg-indigo-600"></div>
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tumbler</span>
                        <span class="p-1 bg-indigo-50 text-indigo-700 rounded-md">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900" x-text="kpis.tumbler ? kpis.tumbler.sold.toLocaleString('id-ID') : 0">0</div>
                    <div class="text-xs text-gray-400 font-medium">Unit Sold</div>
                </div>
                <div class="mt-4 pt-2 border-t border-gray-100 flex justify-between items-center text-xs">
                    <span class="text-gray-500">Stock Avail:</span>
                    <span class="font-bold text-gray-800" x-text="kpis.tumbler ? kpis.tumbler.stock.toLocaleString('id-ID') : 0">0</span>
                </div>
            </div>

            <!-- Card 6: GRAND TOTAL -->
            <div class="bg-amber-100 rounded-xl border border-amber-200 shadow-sm p-4 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 left-0 right-0 h-1 bg-amber-600"></div>
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-amber-800 uppercase tracking-wider">Grand Total</span>
                        <span class="p-1 bg-amber-200 text-amber-900 rounded-md">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                            </svg>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-amber-950" x-text="kpis.grand_total ? kpis.grand_total.sold.toLocaleString('id-ID') : 0">0</div>
                    <div class="text-xs text-amber-700 font-medium">Total Sold</div>
                </div>
                <div class="mt-4 pt-2 border-t border-amber-200 flex justify-between items-center text-xs">
                    <span class="text-amber-800 font-semibold">Total Stock:</span>
                    <span class="font-bold text-amber-950" x-text="kpis.grand_total ? kpis.grand_total.stock.toLocaleString('id-ID') : 0">0</span>
                </div>
            </div>
        </div>

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

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('salesDashboard', () => ({
                // Filter Values
                startDate: '{{ $defaultStart }}',
                endDate: '{{ $defaultEnd }}',
                categoryId: '',
                itemId: '',

                // Data Containers
                items: [],
                // KPIs State
                kpis: {
                    ktm: { sold: 0, stock: 0 },
                    uniform: { sold: 0, stock: 0 },
                    shoes: { sold: 0, stock: 0 },
                    kit: { sold: 0, stock: 0 },
                    tumbler: { sold: 0, stock: 0 },
                    grand_total: { sold: 0, stock: 0 }
                },

                // Chart Instances
                charts: {
                    c1: null,
                    c2: null,
                    c3: null,
                    c4: null,
                    c5: null,
                    c6: null
                },

                init() {
                    // Initial load
                    this.fetchDashboardData();

                    // Watch filter state changes
                    this.$watch('startDate', () => this.onFilterChange());
                    this.$watch('endDate', () => this.onFilterChange());
                    this.$watch('categoryId', () => this.onCategoryChange());
                    this.$watch('itemId', () => this.fetchDashboardData());
                },

                onCategoryChange() {
                    this.itemId = ''; // Reset selected item
                    this.items = []; // Reset items list

                    if (this.categoryId) {
                        axios.get('{{ route("dashboard") }}', {
                            params: {
                                get_items: 1,
                                category_id: this.categoryId
                            }
                        }).then(response => {
                            this.items = response.data;
                        }).catch(error => {
                            console.error('Error fetching items for category:', error);
                        });
                    }
                    this.fetchDashboardData();
                },

                onFilterChange() {
                    this.fetchDashboardData();
                },

                fetchDashboardData() {
                    axios.get('{{ route("dashboard") }}', {
                        params: {
                            ajax: 1,
                            start_date: this.startDate,
                            end_date: this.endDate,
                            category_id: this.categoryId,
                            item_id: this.itemId
                        }
                    }).then(response => {
                        const data = response.data;
                        this.kpis = data.kpis;
                        this.renderCharts(data);
                    }).catch(error => {
                        console.error('Error fetching sales dashboard data:', error);
                    });
                },

                // Formatting Helpers
                formatNumber(num) {
                    if (num >= 1000000000) {
                        return (num / 1000000000).toFixed(1).replace(/\.0$/, '') + 'B';
                    }
                    if (num >= 1000000) {
                        return (num / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
                    }
                    if (num >= 1000) {
                        return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
                    }
                    return num;
                },

                formatRupiah(num) {
                    return 'Rp ' + num.toLocaleString('id-ID');
                },

                renderCharts(data) {
                    const primaryColor = '#980416'; // Maroon
                    const greenColor = '#10B981'; // Emerald
                    const blueColor = '#3B82F6'; // Blue
                    const amberColor = '#F59E0B'; // Amber
                    
                    const sharedOpts = {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1F2937',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 10,
                                borderRadius: 6,
                                boxWidth: 6,
                                boxHeight: 6,
                                usePointStyle: true
                            }
                        }
                    };

                    // Chart 1: Unit Sold by Items
                    if (this.charts.c1) this.charts.c1.destroy();
                    const ctx1 = document.getElementById('c1Chart').getContext('2d');
                    this.charts.c1 = new Chart(ctx1, {
                        type: 'bar',
                        data: data.chart1,
                        options: {
                            ...sharedOpts,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#F3F4F6' },
                                    ticks: {
                                        color: '#6B7280',
                                        callback: (v) => this.formatNumber(v)
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#6B7280', font: { size: 9 } }
                                }
                            }
                        }
                    });

                    // Chart 2: Revenue by Items
                    if (this.charts.c2) this.charts.c2.destroy();
                    const ctx2 = document.getElementById('c2Chart').getContext('2d');
                    this.charts.c2 = new Chart(ctx2, {
                        type: 'bar',
                        data: data.chart2,
                        options: {
                            ...sharedOpts,
                            indexAxis: 'y',
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: { color: '#F3F4F6' },
                                    ticks: {
                                        color: '#6B7280',
                                        callback: (v) => this.formatNumber(v)
                                    }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { color: '#6B7280', font: { size: 9 } }
                                }
                            },
                            plugins: {
                                ...sharedOpts.plugins,
                                tooltip: {
                                    ...sharedOpts.plugins.tooltip,
                                    callbacks: {
                                        label: (context) => ' Revenue: ' + this.formatRupiah(context.parsed.x)
                                    }
                                }
                            }
                        }
                    });

                    // Chart 3: Combo Chart (Total Revenue and Unit Sold by Month)
                    if (this.charts.c3) this.charts.c3.destroy();
                    const ctx3 = document.getElementById('c3Chart').getContext('2d');
                    this.charts.c3 = new Chart(ctx3, {
                        type: 'bar',
                        data: data.chart3,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    type: 'linear',
                                    position: 'left',
                                    grid: { color: '#F3F4F6' },
                                    ticks: {
                                        color: '#6B7280',
                                        callback: (v) => this.formatNumber(v)
                                    },
                                    title: { display: true, text: 'Revenue (Rp)', color: '#4B5563', font: { size: 10, weight: 'bold' } }
                                },
                                y1: {
                                    type: 'linear',
                                    position: 'right',
                                    grid: { drawOnChartArea: false },
                                    ticks: {
                                        color: '#6B7280',
                                        callback: (v) => this.formatNumber(v)
                                    },
                                    title: { display: true, text: 'Unit Sold', color: '#4B5563', font: { size: 10, weight: 'bold' } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#6B7280' }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: { boxWidth: 12, color: '#4B5563', font: { size: 10 } }
                                },
                                tooltip: {
                                    backgroundColor: '#1F2937',
                                    callbacks: {
                                        label: (context) => {
                                            if (context.datasetIndex === 0) {
                                                return ' Revenue: ' + this.formatRupiah(context.parsed.y);
                                            } else {
                                                return ' Unit Sold: ' + context.parsed.y.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Chart 4: Available Stock
                    if (this.charts.c4) this.charts.c4.destroy();
                    const ctx4 = document.getElementById('c4Chart').getContext('2d');
                    this.charts.c4 = new Chart(ctx4, {
                        type: 'bar',
                        data: data.chart4,
                        options: {
                            ...sharedOpts,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#F3F4F6' },
                                    ticks: {
                                        color: '#6B7280',
                                        callback: (v) => this.formatNumber(v)
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#6B7280', font: { size: 9 } }
                                }
                            }
                        }
                    });

                    // Chart 5: Value Stock
                    if (this.charts.c5) this.charts.c5.destroy();
                    const ctx5 = document.getElementById('c5Chart').getContext('2d');
                    this.charts.c5 = new Chart(ctx5, {
                        type: 'bar',
                        data: data.chart5,
                        options: {
                            ...sharedOpts,
                            indexAxis: 'y',
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: { color: '#F3F4F6' },
                                    ticks: {
                                        color: '#6B7280',
                                        callback: (v) => this.formatNumber(v)
                                    }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { color: '#6B7280', font: { size: 9 } }
                                }
                            },
                            plugins: {
                                ...sharedOpts.plugins,
                                tooltip: {
                                    ...sharedOpts.plugins.tooltip,
                                    callbacks: {
                                        label: (context) => ' Stock Value: ' + this.formatRupiah(context.parsed.x)
                                    }
                                }
                            }
                        }
                    });

                    // Chart 6: % Unit Sold (Doughnut)
                    if (this.charts.c6) this.charts.c6.destroy();
                    const ctx6 = document.getElementById('c6Chart').getContext('2d');
                    this.charts.c6 = new Chart(ctx6, {
                        type: 'doughnut',
                        data: data.chart6,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 8,
                                        padding: 10,
                                        color: '#4B5563',
                                        font: { size: 9 }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1F2937',
                                    callbacks: {
                                        label: (context) => {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const value = context.parsed;
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                                            return ` ${context.label}: ${value.toLocaleString('id-ID')} units (${percentage})`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
