# HTML5 QR Scanner

## Apa Itu?

Library JavaScript untuk scan QR Code langsung dari kamera browser — tanpa perlu install app. Berjalan di JavaScript, kompatibel dengan Laravel + Vite.

## Fitur yg Terinstall

| Fitur | Untuk Apa |
|-------|-----------|
| Scan Kamera | Baca QR via kamera HP/laptop (forward & rear) |
| Torch/Flash | Nyalakan senter HP pas scan di tempat gelap |
| Scan File | Upload gambar QR trus di-scan |
| Callback Success | Event pas QR berhasil terbaca |
| Callback Error | Event pas gagal baca QR |
| Continuous Scan | Scan terus-terusan tanpa reload halaman |
| Single Scan | Scan sekali trus berhenti |
| QrBox | Kotak pembatas biar scan lebih akurat |
| Format Support | QR Code, Aztec, Data Matrix, dll |
| Auto Stop | Berhenti scan otomatis setelah sukses |

## 1. Install

Terdapat di `package.json` dengan versi `^2.3.8`.

```bash
npm install html5-qrcode
```

## 2. Setup di resources/js/app.js

```js
import './bootstrap';
import Alpine from 'alpinejs';
import { Html5Qrcode } from 'html5-qrcode';

window.Html5Qrcode = Html5Qrcode;
```

Lalu `npm run build` (Vite). Di Blade, `window.Html5Qrcode` dipakai langsung untuk membuat scanner.

## 3. Contoh Halaman Scan (Staff — `distribution/scan.blade.php`)

Route aktual (name `distribution.*`, role `super_admin|admin|staff`):

| Method | URI | Name | Middleware |
|--------|-----|------|-----------|
| GET | `/distribution/scan` | `distribution.scan.index` | auth, password.changed, role:super_admin\|admin\|staff |
| GET | `/distribution/student/{nim}` | `distribution.scan.student` | auth, password.changed, role:super_admin\|admin\|staff |
| POST | `/distribution/search` | `distribution.search` | + `throttle:30,1` |
| GET | `/distribution/search` | `distribution.search.get` | - |
| POST | `/distribution/process` | `distribution.process` | + `throttle:10,1` |

Skema scan (ringkas): hasil decode = NIM → redirect ke `route('distribution.scan.student', nim)` → halaman detail distribusi.

```js
// resources/views/distribution/scan.blade.php (@push('scripts'))
const html5QrCode = new Html5Qrcode("reader");

html5QrCode.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: { width: 250, height: 250 } },
    function (decodedText) {
        // decodedText = NIM mahasiswa
        html5QrCode.stop();
        window.location.href = `/distribution/student/${decodedText}`;
    },
    function (errorMessage) {
        // ignore scan error (terus scanning)
    }
);
```

**Fallback manual:** input NIM di form → POST `/distribution/search` (`distribution.search`).

## 4. Variasi Kamera

```js
// Kamera depan (selfie buat verifikasi)
{ facingMode: "user" }

// Kamera belakang (default buat scan QR)
{ facingMode: "environment" }

// Semua kamera (user pilih)
Html5Qrcode.getCameras().then(cameras => {
    // cameras = [{id, label}, ...]
});
```

## 5. Scan dari File Upload

```html
<div id="reader-file"></div>
<input type="file" id="qr-input-file" accept="image/*">

<script>
    const scanner = new Html5Qrcode("reader-file");

    document.getElementById('qr-input-file').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        scanner.scanFile(file, true)
            .then(decodedText => {
                alert(`QR berisi: ${decodedText}`);
            })
            .catch(err => {
                alert(`Gagal scan: ${err}`);
            });
    });
</script>
```

## 6. Single Scan (Sekali Lalu Berhenti)

```js
html5QrCode.scan({
    facingMode: "environment"
}).then(decodedText => {
    console.log(`Scanned: ${decodedText}`);
    // html5QrCode.stop() otomatis dipanggil
}).catch(err => {
    console.log(`Scan failed: ${err}`);
});
```

## 7. Integrasi dengan Laravel (Submit via Form)

```js
html5QrCode.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: { width: 250, height: 250 } },
    function (decodedText) {
        html5QrCode.stop();

        // Submit form dengan NIM hasil scan
        document.getElementById('student_id').value = decodedText;
        document.getElementById('scan-form').submit();
    },
    function () {}
);
```

## 8. Struktur File yg Diubah

```
resources/js/app.js                   ← import { Html5Qrcode } + window.Html5Qrcode
resources/views/distribution/scan.blade.php    ← halaman scan (role staff)
resources/views/distribution/distribution.blade.php ← detail mahasiswa setelah scan
routes/web.php                        ← route distribution.scan.* & distribution.search
```

## Sumber
- https://github.com/mebjas/html5-qrcode
- https://scanapp.org (HTML5 QR Scanner demo)

## Analogi
HTML5 QR Scanner itu seperti kasir supermarket — tinggal arahkan kamera ke barcode barang, langsung muncul nama & harganya. Bedanya ini di browser, tanpa install app.
