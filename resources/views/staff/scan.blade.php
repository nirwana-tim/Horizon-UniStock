<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Scan QR / Cari Mahasiswa') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if($activeSchedule)
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                    <p class="text-sm text-blue-700">
                        <strong>Jadwal Aktif:</strong> {{ $activeSchedule->name }}<br>
                        <strong>Lokasi:</strong> {{ $activeSchedule->location }}<br>
                        <strong>Sesi:</strong> {{ $activeSchedule->session }}
                    </p>
                </div>
            @else
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-yellow-700">
                        <strong>Peringatan:</strong> Tidak ada jadwal distribusi aktif untuk hari ini.
                    </p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Scan QR Code</h3>
                        <div id="reader" class="w-full rounded-lg overflow-hidden border border-gray-200" style="min-height: 300px;"></div>
                        <div id="scan-result" class="mt-4"></div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Cari Manual (NIM)</h3>
                        <form action="{{ route('staff.search') }}" method="POST">
                            @csrf
                            <div>
                                <label for="query" class="block text-sm font-medium text-gray-700">NIM Mahasiswa</label>
                                <input type="text" name="query" id="query" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Masukkan NIM mahasiswa">
                                @error('query')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Cari Mahasiswa') }}
                                </button>
                            </div>
                        </form>

                        <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
                            <p class="text-xs text-gray-500">
                                <strong>Petunjuk:</strong><br>
                                - Arahkan kamera ke QR Code mahasiswa<br>
                                - Atau ketik NIM secara manual di kolom atas<br>
                                - QR Code bersifat permanen (1x seumur hidup)
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Html5Qrcode === 'undefined') {
                console.warn('Html5Qrcode library not loaded. QR scanning disabled.');
                document.getElementById('reader').innerHTML =
                    '<div class="p-4 text-center text-gray-500">QR Scanner tidak tersedia. Gunakan pencarian manual NIM.</div>';
                return;
            }

            const html5QrCode = new Html5Qrcode("reader");

            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                function onScanSuccess(decodedText) {
                    document.getElementById('scan-result').innerHTML =
                        `<div class="p-4 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-sm text-green-700">QR terdeteksi: <strong>${decodedText}</strong></p>
                            <p class="text-xs text-green-600 mt-1">Mencari data mahasiswa...</p>
                        </div>`;

                    html5QrCode.stop().then(() => {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("staff.search") }}';

                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'query';
                        input.value = decodedText;
                        form.appendChild(input);

                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';
                        form.appendChild(csrfInput);

                        document.body.appendChild(form);
                        form.submit();
                    }).catch(err => {
                        console.error('Failed to stop scanner:', err);
                        window.location.href = `{{ route("staff.search") }}?query=${encodeURIComponent(decodedText)}`;
                    });
                },
                function onScanFailure(error) {
                    // Silence scan failures - continuous scanning
                }
            ).catch(err => {
                document.getElementById('reader').innerHTML =
                    '<div class="p-4 text-center text-red-500">Gagal mengakses kamera. Gunakan pencarian manual NIM.</div>';
            });
        });
    </script>
    @endpush
</x-app-layout>
