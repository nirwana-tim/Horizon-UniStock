import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Chart from 'chart.js/auto';

Alpine.plugin(collapse);
window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('serverTable', (url) => ({
    search: '',
    page: 1,
    tableHtml: '',
    paginationHtml: '',
    loading: false,
    init() {
        this.fetchData();
    },
    fetchData() {
        this.loading = true;
        axios.get(url, {
            params: { page: this.page, q: this.search }
        })
        .then(res => {
            this.tableHtml = res.data.html || res.data.tableHtml || '';
            this.paginationHtml = res.data.pagination || res.data.paginationHtml || '';
        })
        .catch(err => {
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

Alpine.start();
