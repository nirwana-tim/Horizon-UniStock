<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Item') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('master-data.item.edit', $item) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('master-data.item.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Kembali') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Kode Item</h3>
                            <p class="mt-1 text-sm font-mono text-gray-900">{{ $item->code }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Nama Item</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Kategori</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->category->code ?? '' }} - {{ $item->category->label ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Gender</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ ['L' => 'Laki - Laki', 'P' => 'Perempuan', 'U' => 'Unisex'][$item->gender] ?? $item->gender ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Tipe</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->type?->code ?? '-' }} - {{ $item->type?->label ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Departemen</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->department?->code ?? '-' }} - {{ $item->department?->label ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Satuan</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->unit }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Harga Jual</h3>
                            <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($item->selling_price, 2) }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">HPP</h3>
                            <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($item->hpp, 2) }}</p>
                        </div>
                    </div>

                    {{-- Varian Section --}}
                    <div class="mt-8 pt-4 border-t border-gray-200" x-data="{ showModal: false, selectedSize: '', generatedSku: '', baseCode: '{{ $item->base_code ?? $item->code }}' }">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-500">Varian</h3>
                            <button type="button" @click="showModal = true" class="inline-flex items-center px-4 py-2 bg-[#980416] text-white text-xs font-semibold rounded-lg hover:bg-[#7a0311] transition shadow-sm">
                                + Tambah Varian
                            </button>
                        </div>

                        <!-- Add Variant Modal -->
                        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                            <!-- Backdrop overlay -->
                            <div x-show="showModal"
                                 x-transition:enter="ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>

                            <!-- Modal Positioner -->
                            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                <!-- Modal Panel -->
                                <div x-show="showModal"
                                     x-transition:enter="ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave="ease-in duration-200"
                                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6">
                                    
                                    <form action="{{ route('master-data.item.variant.store', $item) }}" method="POST">
                                        @csrf
                                        <div class="flex items-center justify-between border-b pb-3 mb-4">
                                            <h3 class="text-base font-bold text-gray-900">Tambah Varian Baru</h3>
                                            <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-500 transition">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="space-y-4">
                                            <div>
                                                <x-input-label for="modal_size_id" :value="__('Ukuran')" class="mb-1" />
                                                <select name="size_id" id="modal_size_id" required 
                                                        @change="
                                                            const opt = $event.target.options[$event.target.selectedIndex];
                                                            selectedSize = opt.dataset.code || '';
                                                            generatedSku = selectedSize ? (baseCode + '-' + selectedSize) : '';
                                                        "
                                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 text-sm">
                                                    <option value="">-- Pilih Ukuran --</option>
                                                    @foreach($sizes as $size)
                                                        <option value="{{ $size->id }}" data-code="{{ $size->code }}">{{ $size->code }} - {{ $size->label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <x-input-label for="modal_size" :value="__('Label Ukuran')" class="mb-1" />
                                                <x-text-input type="text" name="size" id="modal_size" required x-model="selectedSize" placeholder="S, M, L, XL, 40, 42" class="w-full text-sm" />
                                            </div>

                                            <div>
                                                <x-input-label for="modal_sku" :value="__('SKU (opsional)')" class="mb-1" />
                                                <x-text-input type="text" name="sku" id="modal_sku" x-model="generatedSku" placeholder="Auto jika kosong" class="w-full text-sm font-mono" />
                                            </div>

                                            <div>
                                                <x-input-label for="modal_weight" :value="__('Berat (opsional dalam kg)')" class="mb-1" />
                                                <x-text-input type="number" name="weight" id="modal_weight" min="0" step="0.01" placeholder="0" class="w-full text-sm" />
                                            </div>
                                        </div>

                                        <div class="mt-6 flex justify-end gap-2 border-t pt-4">
                                            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">Batal</button>
                                            <button type="submit" class="px-4 py-2 bg-primary-700 hover:bg-primary-800 text-white text-xs font-semibold rounded-lg transition shadow-sm">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Varian --}}
                        @if($item->variants->count())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ukuran</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Berat</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($item->variants as $variant)
                                            <tr>
                                                <td class="px-4 py-2 text-sm">{{ $variant->size_label ?? $variant->size }}</td>
                                                <td class="px-4 py-2 text-sm font-mono">{{ $variant->sku }}</td>
                                                <td class="px-4 py-2 text-sm">{{ $variant->weight ? $variant->weight . ' kg' : '-' }}</td>
                                                <td class="px-4 py-2 text-sm text-right">
                                                    <x-delete-modal
                                                        :route="route('master-data.item.variant.destroy', [$item, $variant])"
                                                        label="Hapus Varian"
                                                        description="Apakah Anda yakin ingin menghapus varian {{ $variant->size }} ini? Data ini tidak dapat dikembalikan."
                                                    />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Tidak ada varian.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
