import './bootstrap';
import Alpine from 'alpinejs';
import { Html5Qrcode } from 'html5-qrcode';

window.Html5Qrcode = Html5Qrcode;

window.Alpine = Alpine;

let chartLoadError = false;

window.loadChart = () => import('chart.js/auto').then(m => {
    window.Chart = m.default;
    chartLoadError = false;
    return m.default;
}).catch(err => {
    console.error('Failed to load Chart.js:', err);
    chartLoadError = true;
});

window.activeControllers = [];

Alpine.data('serverTable', (url) => ({
    search: '',
    page: 1,
    perPage: 20,
    facultyId: '',
    studyProgramId: '',
    generationId: '',
    isActive: '',
    period: '',
    category: '',
    gender: '',
    type: '',
    startDate: '',
    endDate: '',
    role: '',
    status: '',
    tableHtml: '',
    paginationHtml: '',
    loading: false,
    _abortController: null,
    _searchTimeout: null,
    init() {
        const tbody = this.$el.querySelector('[x-html="tableHtml"]');
        const pag = this.$el.querySelector('[x-html="paginationHtml"]');
        if (tbody && tbody.innerHTML.trim()) {
            this.tableHtml = tbody.innerHTML;
        }
        if (pag && pag.innerHTML.trim()) {
            this.paginationHtml = pag.innerHTML;
        }
        if (!this.tableHtml) {
            this.fetchData();
        }
    },
    fetchData() {
        if (this._abortController) {
            this._abortController.abort();
        }
        this._abortController = new AbortController();

        this.loading = true;
        const params = { page: this.page };
        if (this.search) params.q = this.search;
        if (this.perPage && this.perPage !== 20) params.per_page = this.perPage;
        if (this.facultyId) params.faculty_id = this.facultyId;
        if (this.studyProgramId) params.study_program_id = this.studyProgramId;
        if (this.generationId) params.generation_id = this.generationId;
        if (this.isActive !== '') params.is_active = this.isActive;
        if (this.period !== '') params.period = this.period;
        if (this.category !== '') params.category = this.category;
        if (this.gender !== '') params.gender = this.gender;
        if (this.type !== '') params.type = this.type;
        if (this.startDate !== '') params.start_date = this.startDate;
        if (this.endDate !== '') params.end_date = this.endDate;
        if (this.role !== '') params.role = this.role;
        if (this.status !== '') params.status = this.status;
        axios.get(url, { params, signal: this._abortController.signal })
        .then(res => {
            this.tableHtml = res.data.html || res.data.tableHtml || '';
            this.paginationHtml = res.data.pagination || res.data.paginationHtml || '';
        })
        .catch(err => {
            if (axios.isCancel(err)) return;
            console.error('serverTable error:', err);
            this.tableHtml = '<tr><td colspan="10" class="px-6 py-4 text-center text-sm text-red-500">Gagal memuat data</td></tr>';
            this.paginationHtml = '';
        })
        .finally(() => { this.loading = false; });
    },
    goToPage(p) {
        this.page = p;
        this.fetchData();
    }
}));

window.toggleItemSizeFlag = function(checkbox) {
    const url = checkbox.dataset.tagUrl;
    const field = checkbox.dataset.field;
    const value = checkbox.checked;
    axios.put(url, { field, value })
        .then(() => {
            checkbox.checked = value;
        })
        .catch(() => {
            checkbox.checked = !value;
        });
};

document.addEventListener('alpine:init', () => {
    // Global touch feedback directive
    Alpine.directive('touch-feedback', (el) => {
        const onStart = () => el.classList.add('opacity-80');
        const onEnd = () => el.classList.remove('opacity-80');
        el.addEventListener('touchstart', onStart, { passive: true });
        el.addEventListener('touchend', onEnd);
        el.addEventListener('touchcancel', onEnd);
    });

    // Global bottom sheet store (vanilla JS approach matching index.html)
    Alpine.store('bottomSheet', {
        overlay: null,
        panel: null,
        body: null,
        isSheetOpen: false,
        _initialized: false,

        _init() {
            if (this._initialized) return;
            this._initialized = true;

            // Create overlay
            this.overlay = document.createElement('div');
            this.overlay.id = 'sheet-overlay';
            this.overlay.className = 'fixed inset-0 bg-black/30 z-[100] opacity-0 pointer-events-none';
            this.overlay.style.display = 'none';
            this.overlay.addEventListener('click', () => this.closeSheet());

            // Create panel
            this.panel = document.createElement('div');
            this.panel.id = 'sheet-panel';
            this.panel.className = 'fixed bottom-0 left-0 right-0 z-[101] bg-white rounded-t-2xl max-h-[85vh] flex flex-col translate-y-full';
            this.panel.style.display = 'none';

            // Handle bar
            const handle = document.createElement('div');
            handle.className = 'sticky top-0 bg-white rounded-t-2xl z-10 px-5 pt-3 pb-2';
            handle.innerHTML = '<div class="w-10 h-1 bg-black/20 rounded-full mx-auto mb-2"></div>';

            // Body
            this.body = document.createElement('div');
            this.body.className = 'overflow-y-auto overscroll-contain px-5 pb-8 flex-1';

            this.panel.appendChild(handle);
            this.panel.appendChild(this.body);

            document.body.appendChild(this.overlay);
            document.body.appendChild(this.panel);

            // ESC key close
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isSheetOpen) this.closeSheet();
            });
        },

        openSheet(html) {
            this._init();
            this.body.innerHTML = html;

            this.overlay.style.display = '';
            this.panel.style.display = '';

            requestAnimationFrame(() => {
                this.overlay.classList.add('opacity-100');
                this.overlay.classList.remove('pointer-events-none');
                this.panel.classList.remove('translate-y-full');
            });
            document.body.classList.add('sheet-open');
            this.isSheetOpen = true;
        },

        closeSheet() {
            if (!this.isSheetOpen || !this.panel) return;
            this.panel.classList.add('translate-y-full');
            this.overlay.classList.remove('opacity-100');
            this.overlay.classList.add('pointer-events-none');
            document.body.classList.remove('sheet-open');
            this.isSheetOpen = false;

            setTimeout(() => {
                if (!this.isSheetOpen) {
                    this.overlay.style.display = 'none';
                    this.panel.style.display = 'none';
                }
            }, 400);
        }
    });

    // ── Modal Content Generators ──
    window.modalContent = function(type) {
        // Schedule detail modal
        if (type.indexOf('schedule-') === 0 || type.indexOf('dist-') === 0) {
            const btn = document.querySelector('[data-modal="' + type + '"]');
            if (!btn) return '';
            const title = btn.getAttribute('data-event-title') || 'Detail Jadwal';
            const start = btn.getAttribute('data-event-start') || '';
            const end = btn.getAttribute('data-event-end') || '';
            const location = btn.getAttribute('data-event-location') || '';
            const note = btn.getAttribute('data-event-note') || '';

            let rows = '';
            if (start || end) {
                const dateText = start && end ? start + ' — ' + end : (start || end);
                rows += '<div class="flex items-start gap-3"><span class="material-symbols-outlined text-primary mt-0.5">calendar_month</span><div><p class="font-body-md text-body-md text-on-surface font-semibold">Tanggal</p><p class="font-body-md text-body-md text-secondary/60">' + dateText + '</p></div></div>';
            }
            if (location) {
                rows += '<div class="flex items-start gap-3"><span class="material-symbols-outlined text-primary mt-0.5">location_on</span><div><p class="font-body-md text-body-md text-on-surface font-semibold">Lokasi</p><p class="font-body-md text-body-md text-secondary/60">' + location + '</p></div></div>';
            }
            if (note) {
                rows += '<div class="mt-5 p-4 bg-primary/5 rounded-xl"><p class="font-body-md text-body-md text-secondary">' + note + '</p></div>';
            }

            return '<div class="py-2">' +
                '<h3 class="font-headline-sm text-headline-sm text-on-surface mb-5">' + title + '</h3>' +
                '<div class="space-y-4">' + rows + '</div>' +
                '<button type="button" class="close-sheet mt-6 w-full h-12 rounded-full bg-primary text-white font-headline-sm text-headline-sm shadow-button active:scale-[0.98] transition-transform duration-200">Tutup</button>' +
                '</div>';
        }

        // Barang detail modal
        if (type.indexOf('barang:') === 0) {
            const id = type.split(':')[1];
            const btn = document.querySelector('[data-modal="barang:' + id + '"]');
            if (!btn) return '';
            const name = btn.getAttribute('data-item-name') || 'Item';
            const size = btn.getAttribute('data-item-size') || '';
            const status = btn.getAttribute('data-item-status') || 'pending';

            let sizeRow = size ? '<div class="flex items-start gap-3"><span class="material-symbols-outlined text-primary mt-0.5">straighten</span><div><p class="font-body-md text-body-md text-on-surface font-semibold">Ukuran</p><p class="font-body-md text-body-md text-secondary/60">' + size + '</p></div></div>' : '';
            const statusClass = status === 'received' ? 'text-emerald-600' : 'text-primary';

            return '<div class="py-2">' +
                '<h3 class="font-headline-sm text-headline-sm text-on-surface mb-5">' + name + '</h3>' +
                '<div class="space-y-4">' +
                '<div class="flex items-start gap-3"><span class="material-symbols-outlined text-primary mt-0.5">tag</span><div><p class="font-body-md text-body-md text-on-surface font-semibold">ID Barang</p><p class="font-body-md text-body-md text-secondary/60">#' + id + '</p></div></div>' +
                sizeRow +
                '<div class="flex items-start gap-3"><span class="material-symbols-outlined text-primary mt-0.5">info</span><div><p class="font-body-md text-body-md text-on-surface font-semibold">Status</p><p class="font-body-md text-body-md ' + statusClass + ' capitalize">' + status + '</p></div></div>' +
                '</div>' +
                '<button type="button" class="close-sheet mt-6 w-full h-12 rounded-full bg-primary text-white font-headline-sm text-headline-sm shadow-button active:scale-[0.98] transition-transform duration-200">Tutup</button>' +
                '</div>';
        }

        // Transaction detail modal
        if (type.indexOf('transaction-') === 0) {
            const btn = document.querySelector('[data-modal="' + type + '"]');
            if (!btn) return '';
            const name = btn.getAttribute('data-tx-name') || 'Transaksi';
            const status = btn.getAttribute('data-tx-status') || 'pending';
            const date = btn.getAttribute('data-tx-date') || '';
            const txNo = btn.getAttribute('data-tx-no') || '';

            const statusClass = status === 'completed' ? 'text-emerald-600' : (status === 'partial' ? 'text-amber-600' : 'text-primary');
            const statusIcon = status === 'completed' ? 'check_circle' : 'schedule';

            let rows = '<div class="flex items-start gap-3"><span class="material-symbols-outlined ' + statusClass + ' mt-0.5">' + statusIcon + '</span><div><p class="font-body-md text-body-md text-on-surface font-semibold">Status</p><p class="font-body-md text-body-md ' + statusClass + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</p></div></div>';
            if (date) {
                rows += '<div class="flex items-start gap-3"><span class="material-symbols-outlined text-primary mt-0.5">calendar_month</span><div><p class="font-body-md text-body-md text-on-surface font-semibold">Tanggal Pengambilan</p><p class="font-body-md text-body-md text-secondary/60">' + date + '</p></div></div>';
            }
            if (txNo) {
                rows += '<div class="flex items-start gap-3"><span class="material-symbols-outlined text-primary mt-0.5">receipt_long</span><div><p class="font-body-md text-body-md text-on-surface font-semibold">No. Transaksi</p><p class="font-body-md text-body-md text-secondary/60">' + txNo + '</p></div></div>';
            }

            return '<div class="py-2">' +
                '<h3 class="font-headline-sm text-headline-sm text-on-surface mb-5">' + name + '</h3>' +
                '<div class="space-y-4">' + rows + '</div>' +
                '<button type="button" class="close-sheet mt-6 w-full h-12 rounded-full bg-primary text-white font-headline-sm text-headline-sm shadow-button active:scale-[0.98] transition-transform duration-200">Tutup</button>' +
                '</div>';
        }

        return '';
    };

    window.filterOptionsHTML = function() {
        return '<div class="py-2">' +
            '<h3 class="font-headline-sm text-headline-sm text-on-surface mb-4">Filter Status</h3>' +
            '<div class="space-y-2">' +
            '<button type="button" class="filter-option w-full text-left px-4 py-3 rounded-xl hover:bg-black/[0.02] transition-colors text-primary font-semibold" data-filter="all">Semua</button>' +
            '<button type="button" class="filter-option w-full text-left px-4 py-3 rounded-xl hover:bg-black/[0.02] transition-colors text-secondary/70" data-filter="pending">Pending</button>' +
            '<button type="button" class="filter-option w-full text-left px-4 py-3 rounded-xl hover:bg-black/[0.02] transition-colors text-secondary/70" data-filter="received">Received</button>' +
            '</div></div>';
    };

    window.passwordFormHTML = function() {
        return '<div class="py-2">' +
            '<h3 class="font-headline-sm text-headline-sm text-on-surface mb-5">Ganti Password</h3>' +
            '<form action="/password" method="POST" class="space-y-4">' +
            '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') + '">' +
            '<input type="hidden" name="_method" value="PUT">' +
            '<input type="text" name="username" autocomplete="username" class="hidden" aria-hidden="true" tabindex="-1">' +
            '<div><label class="block font-label-md text-label-md text-secondary mb-2">Password Saat Ini</label>' +
            '<input type="password" name="current_password" required autocomplete="current-password" class="w-full px-4 h-12 bg-surface-container-low border border-black/[0.08] rounded-xl font-body-md text-body-md text-on-background placeholder-secondary/50 focus:border-primary focus:ring-2 focus:ring-primary/10 transition-colors"></div>' +
            '<div><label class="block font-label-md text-label-md text-secondary mb-2">Password Baru</label>' +
            '<input type="password" name="password" required autocomplete="new-password" class="w-full px-4 h-12 bg-surface-container-low border border-black/[0.08] rounded-xl font-body-md text-body-md text-on-background placeholder-secondary/50 focus:border-primary focus:ring-2 focus:ring-primary/10 transition-colors"></div>' +
            '<div><label class="block font-label-md text-label-md text-secondary mb-2">Konfirmasi Password Baru</label>' +
            '<input type="password" name="password_confirmation" required autocomplete="new-password" class="w-full px-4 h-12 bg-surface-container-low border border-black/[0.08] rounded-xl font-body-md text-body-md text-on-background placeholder-secondary/50 focus:border-primary focus:ring-2 focus:ring-primary/10 transition-colors"></div>' +
            '<button type="submit" class="close-sheet w-full h-12 rounded-full bg-primary text-white font-headline-sm text-headline-sm shadow-button active:scale-[0.98] transition-transform duration-200">Simpan</button>' +
            '</form></div>';
    };

    window.emailsSheetHTML = function(type) {
        return '<div class="py-2">' +
            '<h3 class="font-headline-sm text-headline-sm text-on-surface mb-5">Verifikasi Password</h3>' +
            '<p class="font-body-md text-body-md text-secondary/60 mb-5">Masukkan password kamu untuk melanjutkan perubahan email.</p>' +
            '<form id="verify-password-form" class="space-y-4">' +
            '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') + '">' +
            '<div><label class="block font-label-md text-label-md text-secondary mb-2">Password</label>' +
            '<input type="password" name="password" required autocomplete="current-password" class="w-full px-4 h-12 bg-surface-container-low border border-black/[0.08] rounded-xl font-body-md text-body-md text-on-background placeholder-secondary/50 focus:border-primary focus:ring-2 focus:ring-primary/10 transition-colors" placeholder="Masukkan password"></div>' +
            '<div id="verify-password-error" class="text-[12px] text-error hidden"></div>' +
            '<button type="submit" class="w-full h-12 rounded-full bg-primary text-white font-headline-sm text-headline-sm shadow-button active:scale-[0.98] transition-transform duration-200">Verifikasi</button>' +
            '</form></div>';
    };

    // ── Global Event Delegation ──
    document.addEventListener('click', function(e) {
        // Close-sheet buttons inside bottom sheet
        const closeBtn = e.target.closest('.close-sheet');
        if (closeBtn) {
            e.preventDefault();
            Alpine.store('bottomSheet').closeSheet();
            return;
        }

        // data-modal triggers
        const modalBtn = e.target.closest('[data-modal]');
        if (modalBtn) {
            e.preventDefault();
            var key = modalBtn.getAttribute('data-modal');
            var html = '';
            if (key === 'filter') {
                html = filterOptionsHTML();
            } else if (key === 'change-password') {
                html = passwordFormHTML();
            } else if (key === 'verify-email') {
                html = emailsSheetHTML('kampus');
            } else {
                html = modalContent(key);
            }
            if (html) Alpine.store('bottomSheet').openSheet(html);

            // AJAX handler for verify-password form
            if (key === 'verify-email') {
                setTimeout(function() {
                    var form = document.getElementById('verify-password-form');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();
                            var errorEl = document.getElementById('verify-password-error');
                            if (errorEl) errorEl.classList.add('hidden');

                            var formData = new FormData(form);
                            var btn = form.querySelector('button[type="submit"]');
                            var originalText = btn.textContent;
                            btn.textContent = 'Memverifikasi...';
                            btn.disabled = true;

                            axios.post('/profile/email/verify-password', formData)
                                .then(function(res) {
                                    Alpine.store('bottomSheet').closeSheet();
                                    if (res.data.redirect) {
                                        window.location.href = res.data.redirect;
                                    }
                                })
                                .catch(function(err) {
                                    btn.textContent = originalText;
                                    btn.disabled = false;
                                    if (errorEl) {
                                        var msg = err.response?.data?.error || err.response?.data?.message || 'Password salah. Silakan coba lagi.';
                                        errorEl.textContent = msg;
                                        errorEl.classList.remove('hidden');
                                    }
                                });
                        });
                    }
                }, 100);
            }
            return;
        }

        // data-scroll triggers
        const scrollBtn = e.target.closest('[data-scroll]');
        if (scrollBtn) {
            e.preventDefault();
            var targetId = scrollBtn.getAttribute('data-scroll');
            var target = document.getElementById(targetId);
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            return;
        }

        // data-navigate triggers
        const navBtn = e.target.closest('[data-navigate]');
        if (navBtn) {
            e.preventDefault();
            var url = navBtn.getAttribute('data-navigate');
            if (url) window.location.href = url;
            return;
        }
    });

    Alpine.data('salesDashboard', () => ({
        startDate: '',
        endDate: '',
        categoryId: '',
        itemId: '',
        items: [],
        kpis: {},
        charts: {
            c1: null, c2: null, c3: null,
            c4: null, c5: null, c6: null
        },
        async init() {
            if (chartLoadError) return;
            await window.loadChart();
            this.fetchDashboardData();
            this.$watch('startDate', () => this.onFilterChange());
            this.$watch('endDate', () => this.onFilterChange());
            this.$watch('categoryId', () => this.onCategoryChange());
            this.$watch('itemId', () => this.fetchDashboardData());
        },
        destroy() {
            Object.values(this.charts).forEach(c => { if (c) c.destroy(); });
        },
        onCategoryChange() {
            this.itemId = '';
            this.items = [];
            if (this.categoryId) {
                axios.get(window.DASHBOARD_URL, {
                    params: { get_items: 1, category_id: this.categoryId }
                }).then(response => { this.items = response.data; })
                  .catch(error => { console.error('Error fetching items:', error); });
            }
            this.fetchDashboardData();
        },
        onFilterChange() {
            this.fetchDashboardData();
        },
        fetchDashboardData() {
            if (chartLoadError) return;
            axios.get(window.DASHBOARD_URL, {
                params: {
                    ajax: 1,
                    start_date: this.startDate,
                    end_date: this.endDate,
                    category_id: this.categoryId,
                    item_id: this.itemId
                }
            }).then(response => { this.renderCharts(response.data); })
              .catch(error => { console.error('Error fetching dashboard data:', error); });
        },
        formatNumber(num) {
            if (num >= 1000000000) return (num / 1000000000).toFixed(1).replace(/\.0$/, '') + 'B';
            if (num >= 1000000) return (num / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
            if (num >= 1000) return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
            return num;
        },
        formatRupiah(num) {
            return 'Rp ' + num.toLocaleString('id-ID');
        },
        renderCharts(data) {
            this.kpis = data.kpis || {};

            const primaryColor = '#980416';
            const greenColor = '#10B981';
            const blueColor = '#3B82F6';
            const amberColor = '#F59E0B';

            const sharedOpts = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1F2937',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        borderRadius: 6,
                        boxWidth: 6, boxHeight: 6,
                        usePointStyle: true
                    }
                }
            };

            if (this.charts.c1) this.charts.c1.destroy();
            const ctx1 = document.getElementById('c1Chart')?.getContext('2d');
            if (ctx1) this.charts.c1 = new Chart(ctx1, {
                type: 'bar', data: data.chart1,
                options: { ...sharedOpts, scales: { y: { beginAtZero: true, grid: { color: '#F3F4F6' }, ticks: { color: '#6B7280', callback: (v) => this.formatNumber(v) } }, x: { grid: { display: false }, ticks: { color: '#6B7280', font: { size: 9 } } } } }
            });

            if (this.charts.c2) this.charts.c2.destroy();
            const ctx2 = document.getElementById('c2Chart')?.getContext('2d');
            if (ctx2) this.charts.c2 = new Chart(ctx2, {
                type: 'bar', data: data.chart2,
                options: { ...sharedOpts, indexAxis: 'y', scales: { x: { beginAtZero: true, grid: { color: '#F3F4F6' }, ticks: { color: '#6B7280', callback: (v) => this.formatNumber(v) } }, y: { grid: { display: false }, ticks: { color: '#6B7280', font: { size: 9 } } } }, plugins: { ...sharedOpts.plugins, tooltip: { ...sharedOpts.plugins.tooltip, callbacks: { label: (context) => ' Revenue: ' + this.formatRupiah(context.parsed.x) } } } }
            });

            if (this.charts.c3) this.charts.c3.destroy();
            const ctx3 = document.getElementById('c3Chart')?.getContext('2d');
            if (ctx3) this.charts.c3 = new Chart(ctx3, {
                type: 'bar', data: data.chart3,
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        y: { type: 'linear', position: 'left', grid: { color: '#F3F4F6' }, ticks: { color: '#6B7280', callback: (v) => this.formatNumber(v) }, title: { display: true, text: 'Revenue (Rp)', color: '#4B5563', font: { size: 10, weight: 'bold' } } },
                        y1: { type: 'linear', position: 'right', grid: { drawOnChartArea: false }, ticks: { color: '#6B7280', callback: (v) => this.formatNumber(v) }, title: { display: true, text: 'Unit Sold', color: '#4B5563', font: { size: 10, weight: 'bold' } } },
                        x: { grid: { display: false }, ticks: { color: '#6B7280' } }
                    },
                    plugins: {
                        legend: { display: true, position: 'top', labels: { boxWidth: 12, color: '#4B5563', font: { size: 10 } } },
                        tooltip: { backgroundColor: '#1F2937', callbacks: { label: (context) => context.datasetIndex === 0 ? ' Revenue: ' + this.formatRupiah(context.parsed.y) : ' Unit Sold: ' + context.parsed.y.toLocaleString('id-ID') } }
                    }
                }
            });

            if (this.charts.c4) this.charts.c4.destroy();
            const ctx4 = document.getElementById('c4Chart')?.getContext('2d');
            if (ctx4) this.charts.c4 = new Chart(ctx4, {
                type: 'bar', data: data.chart4,
                options: { ...sharedOpts, scales: { y: { beginAtZero: true, grid: { color: '#F3F4F6' }, ticks: { color: '#6B7280', callback: (v) => this.formatNumber(v) } }, x: { grid: { display: false }, ticks: { color: '#6B7280', font: { size: 9 } } } } }
            });

            if (this.charts.c5) this.charts.c5.destroy();
            const ctx5 = document.getElementById('c5Chart')?.getContext('2d');
            if (ctx5) this.charts.c5 = new Chart(ctx5, {
                type: 'bar', data: data.chart5,
                options: { ...sharedOpts, indexAxis: 'y', scales: { x: { beginAtZero: true, grid: { color: '#F3F4F6' }, ticks: { color: '#6B7280', callback: (v) => this.formatNumber(v) } }, y: { grid: { display: false }, ticks: { color: '#6B7280', font: { size: 9 } } } }, plugins: { ...sharedOpts.plugins, tooltip: { ...sharedOpts.plugins.tooltip, callbacks: { label: (context) => ' Stock Value: ' + this.formatRupiah(context.parsed.x) } } } }
            });

            if (this.charts.c6) this.charts.c6.destroy();
            const ctx6 = document.getElementById('c6Chart')?.getContext('2d');
            if (ctx6) this.charts.c6 = new Chart(ctx6, {
                type: 'doughnut', data: data.chart6,
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'bottom', labels: { boxWidth: 8, padding: 10, color: '#4B5563', font: { size: 9 } } },
                        tooltip: { backgroundColor: '#1F2937', callbacks: { label: (context) => { const total = context.dataset.data.reduce((a, b) => a + b, 0); const pct = total > 0 ? ((context.parsed / total) * 100).toFixed(1) + '%' : '0%'; return ` ${context.label}: ${context.parsed.toLocaleString('id-ID')} units (${pct})`; } } }
                    }
                }
            });
        }
    }));
});

Alpine.start();
