<x-app-layout>
    <div x-data="{
        stats: { totalFaculties: 0, totalStudyPrograms: 0, totalItems: 0, monthlyReceives: 0, draftOpnames: 0, outOfStockItems: 0 },
        lowStockHtml: '',
        init() {
            axios.get('{{ route('dashboard.stats') }}').then(res => { this.stats = res.data; });
            axios.get('{{ route('dashboard.low-stock') }}').then(res => { this.lowStockHtml = res.data.html; });
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
            iconPath="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />

        <x-stat-card
            title="Study Programs"
            value="0"
            color="primary"
            xValue="stats.totalStudyPrograms"
            iconPath="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />

        <x-stat-card
            title="Total Item"
            value="0"
            color="blue"
            xValue="stats.totalItems"
            iconPath="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />

        <x-stat-card
            title="This Month Receives"
            value="0"
            color="green"
            xValue="stats.monthlyReceives"
            iconPath="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />

        <x-stat-card
            title="Opname Draft"
            value="0"
            color="amber"
            xValue="stats.draftOpnames"
            iconPath="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />

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
