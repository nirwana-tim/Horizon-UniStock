# Axios — HTTP Client untuk Request API

**Sumber resmi:** https://axios-http.com/docs/intro  
**Versi terinstall:** `^1.11` (lihat `package.json`)

## Apa Itu Axios?

Axios adalah Promise-based HTTP client untuk browser dan Node.js. Digunakan untuk melakukan request API (GET, POST, PUT, DELETE) dari JavaScript frontend.

## Instalasi & Konfigurasi di Project Ini

**File:** `resources/js/bootstrap.js`

```js
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
```

### Yang Dilakukan:

1. **Import axios** dari node_modules
2. **Daftarkan ke `window.axios`** — bisa diakses dari mana saja
3. **Set header default `X-Requested-With: XMLHttpRequest`** — Laravel otomatis deteksi ini sebagai AJAX request, sehingga response bisa JSON

### Setup CSRF Protection

Laravel secara otomatis menyertakan CSRF token di meta tag layout. Axios sudah terkonfigurasi via `resources/js/bootstrap.js` yang di-generate Breeze.

## Penggunaan Dasar

### GET Request

```js
axios.get('/api/students', {
    params: { page: 1, q: 'andi' }
})
.then(response => {
    console.log(response.data);
})
.catch(error => {
    console.error(error.response?.data || error.message);
});
```

### POST Request

```js
axios.post('/api/students', {
    name: 'Andi',
    nim: '20260001',
    email: 'andi@example.com'
})
.then(response => {
    // sukses
})
.catch(error => {
    if (error.response?.status === 422) {
        console.log(error.response.data.errors);
    }
});
```

### Async/Await

```js
async function fetchData() {
    try {
        const response = await axios.get('/api/items', {
            params: { page: 1, q: 'sepatu' }
        });
        return response.data;
    } catch (error) {
        console.error('Gagal fetch data:', error);
        throw error;
    }
}
```

## Pattern untuk Server-Side Table (Alpine + Axios)

```blade
<div x-data="serverTable()">
    <input type="text" x-model="search" @input.debounce.300ms="fetchData()">
    <div x-html="tableHtml"></div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('serverTable', () => ({
        search: '',
        page: 1,
        tableHtml: '',
        fetchData() {
            axios.get('/master/item/data', {
                params: { page: this.page, q: this.search }
            })
            .then(res => {
                this.tableHtml = res.data.html;
            })
            .catch(err => {
                console.error(err);
            });
        }
    }));
});
</script>
```

## Error Handling

| Status Code | Arti | Handling |
|-------------|------|----------|
| `422` | Validation error | Tampilkan pesan validasi |
| `404` | Not found | Redirect atau notifikasi |
| `500` | Server error | Log error, tampilkan pesan umum |
| `0` / Network Error | Koneksi terputus | Cek koneksi internet / server |

```js
axios.get('/api/data')
    .then(response => {
        // handle sukses
    })
    .catch(error => {
        if (error.response) {
            console.log(error.response.data);
            console.log(error.response.status);
        } else if (error.request) {
            console.log('Network error:', error.message);
        } else {
            console.log('Error:', error.message);
        }
    });
```

## Configuration Default

Semua konfigurasi default bisa diubah via `axios.defaults`:

```js
axios.defaults.baseURL = 'http://127.0.0.1:8000';
axios.defaults.timeout = 5000;
axios.defaults.headers.common['Accept'] = 'application/json';
```

## Sumber

- Dokumentasi resmi Axios: https://axios-http.com/docs/intro
- Request config: https://axios-http.com/docs/req_config
- Response schema: https://axios-http.com/docs/res_schema
- Error handling: https://axios-http.com/docs/handling_errors
