<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Pusat Import Data') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-alert type="info">
                Fitur import data telah diintegrasikan langsung ke masing-masing halaman modul agar lebih mudah digunakan tanpa perlu memilih dari dropdown. Anda juga dapat melakukan import langsung dari kartu di bawah ini.
            </x-alert>

            @if(session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif

            @if(session('error'))
                <x-alert type="error">{{ session('error') }}</x-alert>
            @endif

            {{-- Import Modules Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- 1. Student Import --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between hover:border-primary-300 transition">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2.5 bg-primary-50 text-primary-700 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-base">Data Mahasiswa</h3>
                                <p class="text-xs text-gray-500">Student Profiles & Sizes</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-6">Import mahasiswa baru/lanjutan beserta profil ukuran dari file Excel kampus.</p>
                    </div>
                    <div class="space-y-2 pt-4 border-t border-gray-100">
                        <div class="flex gap-2">
                            <a href="{{ route('templates.download', 'mahasiswa') }}" class="flex-1 text-center px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                Download Template
                            </a>
                            <button type="button" @click="$dispatch('open-modal', 'import-student')" class="flex-1 px-3 py-2 bg-primary-700 hover:bg-primary-800 text-white rounded-lg text-xs font-medium transition">
                                Upload File
                            </button>
                        </div>
                        <a href="{{ route('students.index') }}" class="block text-center text-xs text-primary-700 hover:underline pt-1">
                            Ke Halaman Mahasiswa &rarr;
                        </a>
                    </div>
                </div>

                {{-- 2. Eligibility Import --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between hover:border-primary-300 transition">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2.5 bg-green-50 text-green-700 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-base">Eligibility (DP Lunas)</h3>
                                <p class="text-xs text-gray-500">Status Kelayakan Pembayaran</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-6">Import status kelayakan penerimaan perlengkapan mahasiswa berdasarkan pembayaran DP.</p>
                    </div>
                    <div class="space-y-2 pt-4 border-t border-gray-100">
                        <div class="flex gap-2">
                            <a href="{{ route('templates.download', 'dp_lunas') }}" class="flex-1 text-center px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                Download Template
                            </a>
                            <button type="button" @click="$dispatch('open-modal', 'import-eligibility')" class="flex-1 px-3 py-2 bg-primary-700 hover:bg-primary-800 text-white rounded-lg text-xs font-medium transition">
                                Upload File
                            </button>
                        </div>
                        <a href="{{ route('finance.eligibility.index') }}" class="block text-center text-xs text-primary-700 hover:underline pt-1">
                            Ke Halaman Eligibility &rarr;
                        </a>
                    </div>
                </div>

                {{-- 3. Item Catalog Import --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between hover:border-primary-300 transition">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2.5 bg-blue-50 text-blue-700 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-base">Katalog Barang</h3>
                                <p class="text-xs text-gray-500">Items & Size Variants</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-6">Import daftar master barang/seragam beserta varian ukurannya sekaligus.</p>
                    </div>
                    <div class="space-y-2 pt-4 border-t border-gray-100">
                        <div class="flex gap-2">
                            <a href="{{ route('templates.download', 'katalog') }}" class="flex-1 text-center px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                Download Template
                            </a>
                            <button type="button" @click="$dispatch('open-modal', 'import-item')" class="flex-1 px-3 py-2 bg-primary-700 hover:bg-primary-800 text-white rounded-lg text-xs font-medium transition">
                                Upload File
                            </button>
                        </div>
                        <a href="{{ route('master-data.item.index') }}" class="block text-center text-xs text-primary-700 hover:underline pt-1">
                            Ke Halaman Master Item &rarr;
                        </a>
                    </div>
                </div>

                {{-- 4. Item Price Import --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between hover:border-primary-300 transition">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2.5 bg-purple-50 text-purple-700 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-base">Harga Barang</h3>
                                <p class="text-xs text-gray-500">Selling Price & COGS (HPP)</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-6">Import daftar harga jual dan harga pokok penjualan (HPP) per periode akademik.</p>
                    </div>
                    <div class="space-y-2 pt-4 border-t border-gray-100">
                        <div class="flex gap-2">
                            <a href="{{ route('templates.download', 'harga') }}" class="flex-1 text-center px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                Download Template
                            </a>
                            <button type="button" @click="$dispatch('open-modal', 'import-item-price')" class="flex-1 px-3 py-2 bg-primary-700 hover:bg-primary-800 text-white rounded-lg text-xs font-medium transition">
                                Upload File
                            </button>
                        </div>
                        <a href="{{ route('master-data.item-price.index') }}" class="block text-center text-xs text-primary-700 hover:underline pt-1">
                            Ke Halaman Harga Barang &rarr;
                        </a>
                    </div>
                </div>

                {{-- 5. Item Entitlement Import --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between hover:border-primary-300 transition">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2.5 bg-yellow-50 text-yellow-700 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-base">Hak Barang (Entitlement)</h3>
                                <p class="text-xs text-gray-500">Item Entitlement Mapping</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-6">Import pemetaan jatah hak barang yang didapatkan per prodi dan angkatan.</p>
                    </div>
                    <div class="space-y-2 pt-4 border-t border-gray-100">
                        <div class="flex gap-2">
                            <a href="{{ route('templates.download', 'hak_barang') }}" class="flex-1 text-center px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                Download Template
                            </a>
                            <button type="button" @click="$dispatch('open-modal', 'import-entitlement')" class="flex-1 px-3 py-2 bg-primary-700 hover:bg-primary-800 text-white rounded-lg text-xs font-medium transition">
                                Upload File
                            </button>
                        </div>
                        <a href="{{ route('distribution.entitlement.index') }}" class="block text-center text-xs text-primary-700 hover:underline pt-1">
                            Ke Halaman Entitlements &rarr;
                        </a>
                    </div>
                </div>

                {{-- 6. Stock Receive Import --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between hover:border-primary-300 transition">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2.5 bg-indigo-50 text-indigo-700 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-base">Penerimaan Barang</h3>
                                <p class="text-xs text-gray-500">Stock Receive Entry</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-6">Import bukti transaksi penerimaan stok barang baru masuk dari vendor/supplier.</p>
                    </div>
                    <div class="space-y-2 pt-4 border-t border-gray-100">
                        <div class="flex gap-2">
                            <a href="{{ route('templates.download', 'penerimaan') }}" class="flex-1 text-center px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                Download Template
                            </a>
                            <button type="button" @click="$dispatch('open-modal', 'import-stock-receive')" class="flex-1 px-3 py-2 bg-primary-700 hover:bg-primary-800 text-white rounded-lg text-xs font-medium transition">
                                Upload File
                            </button>
                        </div>
                        <a href="{{ route('inventory.stock-receive.index') }}" class="block text-center text-xs text-primary-700 hover:underline pt-1">
                            Ke Halaman Stock Receive &rarr;
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- Import Modals --}}
    <x-import-modal name="import-student" type="student" template-type="mahasiswa" title="Import Data Mahasiswa" description="Upload file Excel data mahasiswa untuk di-import ke sistem." />
    <x-import-modal name="import-eligibility" type="eligibility" template-type="dp_lunas" title="Import Status Kelayakan (DP Lunas)" description="Upload file Excel status kelayakan DP Lunas mahasiswa." />
    <x-import-modal name="import-item" type="item" template-type="katalog" title="Import Katalog Barang" description="Upload file Excel daftar katalog barang & varian." />
    <x-import-modal name="import-item-price" type="item_price" template-type="harga" title="Import Harga Barang" description="Upload file Excel daftar harga barang & HPP per tahun akademik." />
    <x-import-modal name="import-entitlement" type="entitlement" template-type="hak_barang" title="Import Hak Barang (Entitlement)" description="Upload file Excel daftar hak barang per prodi & level." />
    <x-import-modal name="import-stock-receive" type="stock_receive" template-type="penerimaan" title="Import Penerimaan Stok" description="Upload file Excel data penerimaan barang dari supplier/vendor." />
</x-app-layout>
