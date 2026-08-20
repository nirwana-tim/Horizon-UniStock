<x-app-layout>

    <x-page-header title="Konfigurasi Ukuran" subtitle="Tentukan kategori barang mana yang masuk ke pilihan ukuran baju (huruf) dan sepatu (angka) di halaman Event Ukuran.">
        <x-slot name="actions">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </x-slot>
    </x-page-header>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif

            @if ($errors->any())
                <x-alert type="error">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            <form method="POST" action="{{ route('system.student-size.store') }}">
                @csrf
                @method('PUT')

                {{-- UKURAN BAJU --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Kategori Ukuran BAJU <span class="font-normal text-gray-400 text-sm">(huruf, mis. S/M/L)</span></h3>
                        <p class="text-xs text-gray-500 mt-1">Kategori yang dicentang akan menjadi sumber pilihan ukuran huruf di halaman Buat/Edit Event Ukuran.</p>
                    </div>
                    <div class="p-5">
                        @if ($categories->isEmpty())
                            <p class="text-sm text-gray-500">Belum ada data kategori. Tambahkan kategori barang terlebih dahulu.</p>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach ($categories as $category)
                                    <input type="checkbox"
                                           id="baju-{{ $category->code }}"
                                           name="baju_category_codes[]"
                                           value="{{ $category->code }}"
                                           class="peer sr-only"
                                           {{ in_array($category->code, $bajuCodes, true) ? 'checked' : '' }}>
                                    <label for="baju-{{ $category->code }}"
                                           class="cursor-pointer rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:border-primary-500 peer-checked:bg-primary-700 peer-checked:text-white peer-checked:border-primary-700 transition-colors select-none">
                                        {{ $category->code }} — {{ $category->name }}
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- UKURAN SEPATU --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Kategori Ukuran SEPATU <span class="font-normal text-gray-400 text-sm">(angka, mis. 40-44)</span></h3>
                        <p class="text-xs text-gray-500 mt-1">Kategori yang dicentang akan menjadi sumber pilihan ukuran angka di halaman Buat/Edit Event Ukuran.</p>
                    </div>
                    <div class="p-5">
                        @if ($categories->isEmpty())
                            <p class="text-sm text-gray-500">Belum ada data kategori. Tambahkan kategori barang terlebih dahulu.</p>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach ($categories as $category)
                                    <input type="checkbox"
                                           id="sepatu-{{ $category->code }}"
                                           name="sepatu_category_codes[]"
                                           value="{{ $category->code }}"
                                           class="peer sr-only"
                                           {{ in_array($category->code, $sepatuCodes, true) ? 'checked' : '' }}>
                                    <label for="sepatu-{{ $category->code }}"
                                           class="cursor-pointer rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:border-primary-500 peer-checked:bg-primary-700 peer-checked:text-white peer-checked:border-primary-700 transition-colors select-none">
                                        {{ $category->code }} — {{ $category->name }}
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                    <strong>Catatan:</strong> jika tidak ada kategori yang dicentang, sistem otomatis memakai nilai default dari file
                    <code class="text-xs bg-amber-100 px-1 py-0.5 rounded">config/student-size.php</code>.
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2 text-sm font-medium transition">
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>