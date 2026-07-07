import Alpine from 'alpinejs';

Alpine.data('serverTable', (url) => ({
    search: new URLSearchParams(window.location.search).get('q') || '',
    page: parseInt(new URLSearchParams(window.location.search).get('page')) || 1,
    tableHtml: '',

    init() {
        this.fetchData();
    },

    fetchData() {
        axios.get(url, {
            params: {
                page: this.page,
                q: this.search,
            },
        })
        .then((res) => {
            this.tableHtml = res.data.html;
        })
        .catch((err) => {
            console.error('Server table error:', err);
        });
    },

    goToPage(page) {
        this.page = page;
        this.fetchData();
    },
}));
