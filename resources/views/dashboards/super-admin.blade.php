<x-app-layout>
    <div x-data="{
        stats: { totalUsers: 0, totalStudents: 0, totalItems: 0, totalStockReceives: 0, outOfStockItems: 0 },
        lowStockHtml: '',
        init() {
            axios.get('{{ route('dashboard.stats') }}').then(res => { this.stats = res.data; });
            axios.get('{{ route('dashboard.low-stock') }}').then(res => { this.lowStockHtml = res.data.html; });
        }
    }">

    <x-page-header title="Dashboard Super Admin" subtitle="Monitor semua aktivitas dan sistem UniStock">
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

    {{-- Sales Charts — 6 Chart Grid 2×3 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

        {{-- Chart 1: Unit Sold by Item (kolom) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <h4 class="text-xs font-semibold text-gray-600 mb-3">Unit Sold by Item</h4>
            <div class="relative" style="height:170px">
                <canvas id="c1Chart"></canvas>
                <div id="c1Empty" class="hidden absolute inset-0 flex items-center justify-center text-gray-400 text-xs">Belum ada data</div>
            </div>
        </div>

        {{-- Chart 2: Revenue by Category (barang bertumpuk) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <h4 class="text-xs font-semibold text-gray-600 mb-3">Revenue by Category</h4>
            <div class="relative" style="height:170px">
                <canvas id="c2Chart"></canvas>
                <div id="c2Empty" class="hidden absolute inset-0 flex items-center justify-center text-gray-400 text-xs">Belum ada data</div>
            </div>
        </div>

        {{-- Chart 3: Revenue + Unit Sold by Month (kombinasi) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <h4 class="text-xs font-semibold text-gray-600 mb-3">Monthly Revenue &amp; Unit Sold</h4>
            <div class="relative" style="height:170px">
                <canvas id="c3Chart"></canvas>
                <div id="c3Empty" class="hidden absolute inset-0 flex items-center justify-center text-gray-400 text-xs">Belum ada data</div>
            </div>
        </div>

        {{-- Chart 4: Available Stock by Item (kolom) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <h4 class="text-xs font-semibold text-gray-600 mb-3">Available Stock by Item</h4>
            <div class="relative" style="height:170px">
                <canvas id="c4Chart"></canvas>
                <div id="c4Empty" class="hidden absolute inset-0 flex items-center justify-center text-gray-400 text-xs">Belum ada data</div>
            </div>
        </div>

        {{-- Chart 5: Value Stock by Category (batang bertumpuk) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <h4 class="text-xs font-semibold text-gray-600 mb-3">Value Stock by Category</h4>
            <div class="relative" style="height:170px">
                <canvas id="c5Chart"></canvas>
                <div id="c5Empty" class="hidden absolute inset-0 flex items-center justify-center text-gray-400 text-xs">Belum ada data</div>
            </div>
        </div>

        {{-- Chart 6: % Unit Sold by Item (lingkaran) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <h4 class="text-xs font-semibold text-gray-600 mb-3">% Unit Sold by Item</h4>
            <div class="relative" style="height:170px">
                <canvas id="c6Chart"></canvas>
                <div id="c6Empty" class="hidden absolute inset-0 flex items-center justify-center text-gray-400 text-xs">Belum ada data</div>
            </div>
        </div>

    </div>

    </div>
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        axios.get('{{ route('dashboard.sales-chart') }}').then(res => {
            const primary = '#980416';
            const palette = ['#980416','#C0392B','#E74C3C','#F1948A','#FADBD8','#6B0000','#A93226','#7B241C','#D98880','#F5B7B1'];

            // --- Chart 1: Unit Sold by Item (kolom) ---
            if (res.data.c1Labels?.length) {
                new Chart(document.getElementById('c1Chart'), {
                    type: 'bar',
                    data: {
                        labels: res.data.c1Labels,
                        datasets: [{ label: 'Unit Sold', data: res.data.c1Data, backgroundColor: primary, borderRadius: 3 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { ticks: { font: { size: 9 } } }, x: { ticks: { font: { size: 8 }, maxRotation: 45 } } }
                    }
                });
            } else { toggleEmpty('c1'); }

            // --- Chart 2: Revenue by Category (barang bertumpuk) ---
            if (res.data.c2Categories?.length) {
                const ds = res.data.c2Datasets;
                new Chart(document.getElementById('c2Chart'), {
                    type: 'bar',
                    data: {
                        labels: res.data.c2Categories,
                        datasets: ds.map((d, i) => ({ label: d.label, data: d.data, backgroundColor: palette[i % palette.length], borderRadius: 2 }))
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        scales: {
                            x: { stacked: true },
                            y: { stacked: true, ticks: { font: { size: 9 }, callback: v => 'Rp' + (v/1000000).toFixed(0) + 'jt' } }
                        },
                        plugins: { legend: { display: false } }
                    }
                });
            } else { toggleEmpty('c2'); }

            // --- Chart 3: Revenue + Unit Sold by Month (kombinasi) ---
            if (res.data.months?.length) {
                new Chart(document.getElementById('c3Chart'), {
                    type: 'bar',
                    data: {
                        labels: res.data.months,
                        datasets: [
                            { label: 'Revenue', type: 'bar', data: res.data.revenue, backgroundColor: primary, borderRadius: 3, yAxisID: 'y' },
                            { label: 'Unit Sold', type: 'line', data: res.data.units, borderColor: '#2563EB', backgroundColor: 'transparent', tension: 0.3, pointRadius: 3, yAxisID: 'y1' }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        scales: {
                            y: { position: 'left', ticks: { font: { size: 9 }, callback: v => (v/1000000).toFixed(0) + 'jt' } },
                            y1: { position: 'right', grid: { drawOnChartArea: false }, ticks: { font: { size: 9 } } }
                        },
                        plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 9 } } } }
                    }
                });
            } else { toggleEmpty('c3'); }

            // --- Chart 4: Available Stock by Item (kolom) ---
            if (res.data.c4Labels?.length) {
                new Chart(document.getElementById('c4Chart'), {
                    type: 'bar',
                    data: {
                        labels: res.data.c4Labels,
                        datasets: [{ label: 'Stock', data: res.data.c4Data, backgroundColor: primary, borderRadius: 3 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { ticks: { font: { size: 9 } } }, x: { ticks: { font: { size: 8 }, maxRotation: 45 } } }
                    }
                });
            } else { toggleEmpty('c4'); }

            // --- Chart 5: Value Stock by Category (batang bertumpuk) ---
            if (res.data.c5Categories?.length) {
                const ds5 = res.data.c5Datasets;
                new Chart(document.getElementById('c5Chart'), {
                    type: 'bar',
                    data: {
                        labels: res.data.c5Categories,
                        datasets: ds5.map((d, i) => ({ label: d.label, data: d.data, backgroundColor: palette[i % palette.length], borderRadius: 2 }))
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        scales: {
                            x: { stacked: true },
                            y: { stacked: true, ticks: { font: { size: 9 }, callback: v => 'Rp' + (v/1000000).toFixed(0) + 'jt' } }
                        },
                        plugins: { legend: { display: false } }
                    }
                });
            } else { toggleEmpty('c5'); }

            // --- Chart 6: % Unit Sold by Item (lingkaran) ---
            if (res.data.c6Labels?.length) {
                new Chart(document.getElementById('c6Chart'), {
                    type: 'doughnut',
                    data: {
                        labels: res.data.c6Labels,
                        datasets: [{
                            data: res.data.c6Data,
                            backgroundColor: palette.slice(0, res.data.c6Labels.length),
                            borderWidth: 1,
                            borderColor: '#fff',
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        cutout: '55%',
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 8, padding: 4, font: { size: 8 } } }
                        }
                    }
                });
            } else { toggleEmpty('c6'); }

            function toggleEmpty(prefix) {
                document.getElementById(prefix + 'Chart').classList.add('hidden');
                document.getElementById(prefix + 'Empty').classList.remove('hidden');
            }
        }).catch(e => console.error('salesChart:', e));
    });
    </script>
    @endpush
</x-app-layout>
