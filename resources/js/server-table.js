import Alpine from 'alpinejs';

Alpine.data('serverTable', (url) => ({
    search: new URLSearchParams(window.location.search).get('q') || '',
    page: parseInt(new URLSearchParams(window.location.search).get('page')) || 1,
    tableHtml: '',
    paginationHtml: '',

    init() {
        this.fetchData();
        this.$el.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-page]');
            if (btn) {
                e.preventDefault();
                this.goToPage(parseInt(btn.dataset.page));
            }
        });
    },

    fetchData() {
        axios.get(url, {
            params: {
                page: this.page,
                q: this.search,
            },
        })
        .then((res) => {
            if (res.data && res.data.html) {
                this.tableHtml = res.data.html;
            }
            if (res.data && res.data.pagination) {
                this.paginationHtml = res.data.pagination;
            }
        })
        .catch((err) => {
            this.tableHtml = '<div class="p-6 text-center"><p class="text-sm text-red-500">Gagal memuat data. Silakan muat ulang halaman.</p></div>';
            this.paginationHtml = '';
        });
    },

    goToPage(page) {
        this.page = page;
        this.fetchData();
    },
}));
