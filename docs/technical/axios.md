# Axios — HTTP Client untuk Request API

**Sumber resmi:** https://axios-http.com/docs/intro  
**Versi terinstall:** `^1.11.0` (lihat `package.json`)

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

Laravel secara otomatis menyertakan CSRF token di meta tag layout (`<meta name="csrf-token">`). Axios sudah terkonfigurasi via `resources/js/bootstrap.js`.

### Penggunaan aktual di proyek UniStock (`resources/js/app.js`)

| Penggunaan | Lokasi | Fungsi |
|------------|--------|--------|
| `serverTable(url)` | Alpine data component | Memuat HTML tabel + pagination via AJAX (dengan `AbortController` + cancel) |
| `salesDashboard` | Alpine data component (dashboard admin) | Fetch KPI + data chart dari `DASHBOARD_URL` dengan filter tanggal/kategori/item |
| Verifikasi password ganti email | Global event delegation (profile) | `POST /profile/email/verify-password` untuk verifikasi password sebelum ganti email |
| Ajax chart params | `GET window.DASHBOARD_URL` | `ajax=1`, `start_date`, `end_date`, `category_id`, `item_id` |

Semua request axios dikirim dengan header `X-Requested-With: XMLHttpRequest` (dari `bootstrap.js`), sehingga Laravel mengembalikan response JSON.

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

Di UniStock, setiap tabel pakai component Alpine `serverTable(url)` yang sudah didefinisikan di `app.js`:

```blade
<div x-data="serverTable(@js(route('students.index', ['ajax' => 1])))">
    <input type="search" x-model="search" @input.debounce.300ms="fetchData()">
    <table>
        <tbody x-html="tableHtml"></tbody>
    </table>
    <div x-html="paginationHtml"></div>
</div>
```

Komponen ini mengirim param `page`, `q` (pencarian), `per_page`, `faculty_id`, `study_program_id`, `generation_id`, `is_active` ke URL yang sama, lalu menampilkan `html` dan `pagination` dari response.

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
