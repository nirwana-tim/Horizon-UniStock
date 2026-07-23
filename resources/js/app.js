import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;

window.loadChart = () => import('chart.js/auto').then(m => {
    window.Chart = m.default;
    return m.default;
});

Alpine.data('serverTable', (url) => ({
    search: '',
    page: 1,
    perPage: 20,
    studyProgramId: '',
    generationId: '',
    tableHtml: '',
    paginationHtml: '',
    loading: false,
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
        this.loading = true;
        const params = { page: this.page };
        if (this.search) params.q = this.search;
        if (this.perPage && this.perPage !== 20) params.per_page = this.perPage;
        if (this.studyProgramId) params.study_program_id = this.studyProgramId;
        if (this.generationId) params.generation_id = this.generationId;
        axios.get(url, { params })
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
