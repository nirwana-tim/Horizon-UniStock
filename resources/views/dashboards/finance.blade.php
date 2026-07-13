<x-app-layout>
    <div x-data="{
        loading: true,
        stats: { totalFaculties: 0, totalStudyPrograms: 0, totalItems: 0, monthlyReceives: 0, draftOpnames: 0, outOfStockItems: 0 },
        lowStockHtml: '',
        init() {
            axios.get('{{ route('dashboard.stats') }}').then(res => {
                this.stats = res.data;
                this.loading = false;
            });
            axios.get('{{ route('dashboard.low-stock') }}').then(res => {
                this.lowStockHtml = res.data.html;
            });
        }
    }">

    <x-page-header title="Finance Dashboard" subtitle="Monitor uniform distribution, stock, and system activity">
        <x-slot name="breadcrumb">
            <span class="text-gray-800 font-medium">Dashboard</span>
        </x-slot>
    </x-page-header>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">

        <x-stat-card
            title="Faculties"
            value="0"
            color="primary"
            xValue="stats.totalFaculties"
            :loading="false"
            x-show="!loading"
            iconPath="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />

        <x-stat-card
            title="Faculties"
            value="0"
            color="primary"
            loading
            x-show="loading"
            iconPath="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />

        <x-stat-card
            title="Study Programs"
            value="0"
            color="blue"
            xValue="stats.totalStudyPrograms"
            :loading="false"
            x-show="!loading"
            iconPath="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />

        <x-stat-card
            title="Study Programs"
            value="0"
            color="blue"
            loading
            x-show="loading"
            iconPath="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />

        <x-stat-card
            title="Total Item"
            value="0"
            color="green"
            xValue="stats.totalItems"
            :loading="false"
            x-show="!loading"
            iconPath="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />

        <x-stat-card
            title="Total Item"
            value="0"
            color="green"
            loading
            x-show="loading"
            iconPath="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />

        <x-stat-card
            title="This Month Receives"
            value="0"
            color="teal"
            xValue="stats.monthlyReceives"
            :loading="false"
            x-show="!loading"
            iconPath="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />

        <x-stat-card
            title="This Month Receives"
            value="0"
            color="teal"
            loading
            x-show="loading"
            iconPath="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />

        <x-stat-card
            title="Opname Draft"
            value="0"
            color="amber"
            xValue="stats.draftOpnames"
            :loading="false"
            x-show="!loading"
            iconPath="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />

        <x-stat-card
            title="Opname Draft"
            value="0"
            color="amber"
            loading
            x-show="loading"
            iconPath="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />

    </div>

    {{-- Low Stock Alert --}}
    <div x-html="lowStockHtml"></div>

    {{-- Section Label --}}
    <div class="flex items-center gap-3 mb-4">
        <h3 class="text-sm font-semibold text-gray-700">Sales &amp; Stock Overview</h3>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    {{-- Charts Grid 2×3 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

        {{-- Chart 1: Unit Sold by Item --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Unit Sold by Item</h4>
            <div class="relative" style="height:220px">
                <canvas id="c1Chart"></canvas>
                <div id="c1Empty" class="hidden absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span class="text-xs">Belum ada data</span>
                </div>
            </div>
        </div>

        {{-- Chart 2: Revenue by Category --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Revenue by Category</h4>
            <div class="relative" style="height:220px">
                <canvas id="c2Chart"></canvas>
                <div id="c2Empty" class="hidden absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span class="text-xs">Belum ada data</span>
                </div>
            </div>
        </div>

        {{-- Chart 3: Monthly Revenue & Unit Sold --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Monthly Revenue &amp; Unit Sold</h4>
            <div class="relative" style="height:220px">
                <canvas id="c3Chart"></canvas>
                <div id="c3Empty" class="hidden absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span class="text-xs">Belum ada data</span>
                </div>
            </div>
        </div>

        {{-- Chart 4: Available Stock by Item --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Available Stock by Item</h4>
            <div class="relative" style="height:220px">
                <canvas id="c4Chart"></canvas>
                <div id="c4Empty" class="hidden absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    <span class="text-xs">Belum ada data</span>
                </div>
            </div>
        </div>

        {{-- Chart 5: Value Stock by Category --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Value Stock by Category</h4>
            <div class="relative" style="height:220px">
                <canvas id="c5Chart"></canvas>
                <div id="c5Empty" class="hidden absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    <span class="text-xs">Belum ada data</span>
                </div>
            </div>
        </div>

        {{-- Chart 6: % Unit Sold by Item --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">% Unit Sold by Item</h4>
            <div class="relative" style="height:220px">
                <canvas id="c6Chart"></canvas>
                <div id="c6Empty" class="hidden absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                    <span class="text-xs">Belum ada data</span>
                </div>
            </div>
        </div>

    </div>

    </div>
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        axios.get('{{ route('dashboard.sales-chart') }}').then(res => {
            const primary = '#980416';
            const palette = ['#980416','#2563EB','#059669','#D97706','#7C3AED','#DC2626','#0891B2','#DB2777','#65A30D','#EA580C'];

            function toggleEmpty(prefix) {
                document.getElementById(prefix + 'Chart').classList.add('hidden');
                document.getElementById(prefix + 'Empty').classList.remove('hidden');
            }

            const tickFont = { size: 10 };
            const labelFont = { size: 10 };

            const sharedOpts = {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 800, easing: 'easeOutQuart' },
                interaction: { mode: 'nearest', axis: 'x', intersect: false },
                plugins: {
                    tooltip: { usePointStyle: true, backgroundColor: '#1F2937', titleFont: { size: 11 }, bodyFont: { size: 10 }, padding: 8, cornerRadius: 6 }
                }
            };

            if (res.data.c1Labels?.length) {
                new Chart(document.getElementById('c1Chart'), {
                    type: 'bar',
                    data: {
                        labels: res.data.c1Labels,
                        datasets: [{ label: 'Unit Sold', data: res.data.c1Data, backgroundColor: primary, borderRadius: 4, barThickness: 18 }]
                    },
                    options: {
                        ...sharedOpts,
                        indexAxis: 'y',
                        plugins: { ...sharedOpts.plugins, legend: { display: false } },
                        scales: { x: { ticks: { font: tickFont } }, y: { ticks: { font: labelFont } } }
                    }
                });
            } else { toggleEmpty('c1'); }

            if (res.data.c2Categories?.length) {
                const ds = res.data.c2Datasets;
                new Chart(document.getElementById('c2Chart'), {
                    type: 'line',
                    data: {
                        labels: res.data.c2Categories,
                        datasets: ds.map((d, i) => ({
                            label: d.label,
                            data: d.data,
                            borderColor: palette[i % palette.length],
                            backgroundColor: palette[i % palette.length],
                            fill: false,
                            tension: 0.3,
                            pointStyle: 'circle',
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBackgroundColor: palette[i % palette.length],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                        }))
                    },
                    options: {
                        ...sharedOpts,
                        scales: {
                            y: { ticks: { font: tickFont, callback: v => 'Rp' + (v/1000000).toFixed(1) + 'jt' } }
                        },
                        plugins: {
                            ...sharedOpts.plugins,
                            legend: { position: 'top', labels: { boxWidth: 12, font: { size: 9 }, padding: 8 } }
                        }
                    }
                });
            } else { toggleEmpty('c2'); }

            if (res.data.months?.length) {
                new Chart(document.getElementById('c3Chart'), {
                    type: 'bar',
                    data: {
                        labels: res.data.months,
                        datasets: [
                            {
                                label: 'Revenue',
                                type: 'bar',
                                data: res.data.revenue,
                                backgroundColor: primary,
                                borderRadius: 4,
                                yAxisID: 'y',
                                barThickness: 16
                            },
                            {
                                label: 'Unit Sold',
                                type: 'line',
                                data: res.data.units,
                                borderColor: '#2563EB',
                                backgroundColor: 'rgba(37,99,235,0.10)',
                                fill: 'origin',
                                tension: 0.4,
                                pointStyle: 'circle',
                                pointRadius: 4,
                                pointHoverRadius: 8,
                                pointBackgroundColor: '#2563EB',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        ...sharedOpts,
                        scales: {
                            y: { position: 'left', ticks: { font: tickFont, callback: v => (v/1000000).toFixed(1) + 'jt' } },
                            y1: { position: 'right', grid: { drawOnChartArea: false }, ticks: { font: tickFont } }
                        },
                        plugins: {
                            ...sharedOpts.plugins,
                            legend: { position: 'top', labels: { boxWidth: 12, font: { size: 10 }, padding: 12 } }
                        }
                    }
                });
            } else { toggleEmpty('c3'); }

            if (res.data.c4Labels?.length) {
                new Chart(document.getElementById('c4Chart'), {
                    type: 'polarArea',
                    data: {
                        labels: res.data.c4Labels,
                        datasets: [{
                            data: res.data.c4Data,
                            backgroundColor: palette.slice(0, res.data.c4Labels.length),
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        ...sharedOpts,
                        plugins: {
                            ...sharedOpts.plugins,
                            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 6, font: { size: 10 } } }
                        }
                    }
                });
            } else { toggleEmpty('c4'); }

            if (res.data.c5Categories?.length) {
                const ds5 = res.data.c5Datasets;
                new Chart(document.getElementById('c5Chart'), {
                    type: 'doughnut',
                    data: {
                        labels: res.data.c5Categories,
                        datasets: [{
                            data: ds5.map(d => d.data.reduce((a, b) => a + b, 0)),
                            backgroundColor: palette.slice(0, ds5.length),
                            borderWidth: 2,
                            borderColor: '#fff',
                        }]
                    },
                    options: {
                        ...sharedOpts,
                        cutout: '65%',
                        plugins: {
                            ...sharedOpts.plugins,
                            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 6, font: { size: 10 } } }
                        }
                    }
                });
            } else { toggleEmpty('c5'); }

            if (res.data.c6Labels?.length) {
                new Chart(document.getElementById('c6Chart'), {
                    type: 'doughnut',
                    data: {
                        labels: res.data.c6Labels,
                        datasets: [{
                            data: res.data.c6Data,
                            backgroundColor: palette.slice(0, res.data.c6Labels.length),
                            borderWidth: 2,
                            borderColor: '#fff',
                            spacing: 4,
                        }]
                    },
                    options: {
                        ...sharedOpts,
                        cutout: '60%',
                        plugins: {
                            ...sharedOpts.plugins,
                            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 6, font: { size: 10 } } }
                        }
                    }
                });
            } else { toggleEmpty('c6'); }
        }).catch(e => console.error('salesChart:', e));
    });
    </script>
    @endpush
</x-app-layout>
