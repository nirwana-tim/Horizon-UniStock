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

```bash
npm install html5-qrcode
```

## 2. Setup di resources/js/app.js

```js
import './bootstrap';
import Alpine from 'alpinejs';
import 'html5-qrcode';
```

Lalu `npm run build`.

## 3. Contoh Halaman Scan Sederhana

**`resources/views/scan.blade.php`**
```blade
<x-app-layout>
    <div class="p-6">
        <h1>Scan QR Mahasiswa</h1>

        <div id="reader" style="width: 400px"></div>

        <div id="result" class="mt-4 p-4 bg-gray-100 rounded"></div>

        <form id="manual-form" class="mt-4" method="POST" action="{{ route('scan.manual') }}">
            @csrf
            <label>Atau input manual NIM:</label>
            <input type="text" name="student_id" class="border rounded p-2" placeholder="Ketik NIM">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Cari</button>
        </form>
    </div>
</x-app-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const html5QrCode = new Html5Qrcode("reader");

        html5QrCode.start(
            { facingMode: "environment" }, // kamera belakang
            { fps: 10, qrbox: { width: 250, height: 250 } },
            function (decodedText) {
                // Sukses scan
                document.getElementById('result').innerHTML =
                    `<p class="text-green-600">NIM terdeteksi: <strong>${decodedText}</strong></p>
                     <p class="mt-2">Mengarahkan...</p>`;

                html5QrCode.stop();

                // Redirect ke halaman detail mahasiswa
                window.location.href = `/students/${decodedText}`;
            },
            function (errorMessage) {
                // ignore scan error (terus scanning)
            }
        );
    });
</script>
@endpush
```

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
resources/js/app.js    ← import 'html5-qrcode'
resources/views/       ← halaman scan blade
routes/web.php         ← route untuk scan & redirect
```

## Sumber
- https://github.com/mebjas/html5-qrcode
- https://scanapp.org (HTML5 QR Scanner demo)

## Analogi
HTML5 QR Scanner itu seperti kasir supermarket — tinggal arahkan kamera ke barcode barang, langsung muncul nama & harganya. Bedanya ini di browser, tanpa install app.
