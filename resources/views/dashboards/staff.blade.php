<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard Staff') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pilih Metode Pencarian Mahasiswa</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <a href="{{ route('staff.scan.index') }}" class="block p-6 bg-gray-50 border-2 border-gray-200 rounded-xl hover:border-indigo-400 hover:bg-indigo-50 transition text-center">
                            <div class="text-4xl mb-3">📷</div>
                            <h4 class="text-base font-semibold text-gray-900">Scan QR Mahasiswa</h4>
                            <p class="mt-1 text-sm text-gray-500">Gunakan kamera untuk scan QR permanen mahasiswa</p>
                        </a>
                        <button type="button" onclick="document.getElementById('nim-form').classList.toggle('hidden')" class="block p-6 bg-gray-50 border-2 border-gray-200 rounded-xl hover:border-indigo-400 hover:bg-indigo-50 transition text-center">
                            <div class="text-4xl mb-3">🔍</div>
                            <h4 class="text-base font-semibold text-gray-900">Cari Manual NIM</h4>
                            <p class="mt-1 text-sm text-gray-500">Ketik NIM mahasiswa untuk melanjutkan</p>
                        </button>
                    </div>

                    <div id="nim-form" class="hidden mt-6 p-4 bg-gray-50 rounded-lg">
                        <form action="{{ route('staff.search') }}" method="POST">
                            @csrf
                            <x-input-label for="nim" value="Masukkan NIM" />
                            <div class="mt-1 flex gap-2">
                                <x-text-input id="nim" name="nim" type="text" class="flex-1" placeholder="Ketik NIM mahasiswa" required />
                                <x-primary-button class="!bg-gray-800 hover:!bg-gray-700">Cari</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Panduan Singkat</h3>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600">
                        <li>Scan QR mahasiswa menggunakan kamera, atau cari manual via NIM</li>
                        <li>Sistem akan menampilkan data mahasiswa dan entitlement tahap aktif</li>
                        <li>Centang item yang diberikan, edit ukuran jika perlu</li>
                        <li>Validasi stok, jika kurang bisa partial pickup</li>
                        <li>Submit transaksi untuk menyimpan data distribusi</li>
                    </ol>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
