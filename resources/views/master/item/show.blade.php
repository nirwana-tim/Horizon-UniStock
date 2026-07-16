<x-app-layout>
    <div class="max-w-7xl mx-auto">

        <x-page-header title="{{ $item->name }}">
            <x-slot name="breadcrumb">
                <a href="{{ route('master-data.item.index') }}" class="hover:text-primary-700 transition-colors">Item</a>
                <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-800 font-medium">Detail</span>
            </x-slot>
            <x-slot name="actions">
                <a href="{{ route('master-data.item.edit', $item) }}"
                   class="inline-flex items-center px-4 py-2 bg-amber-500 text-white text-xs font-semibold rounded-lg hover:bg-amber-600 transition shadow-sm">
                    Edit
                </a>
                <a href="{{ route('master-data.item.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-300 transition">
                    Kembali
                </a>
            </x-slot>
        </x-page-header>

        @if(session('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif

        {{-- Info Item --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode Item</p>
                    <p class="mt-1 text-sm font-mono text-gray-900">{{ $item->code }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Item</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->category?->label ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Gender</p>
                    <p class="mt-1 text-sm text-gray-900">{{ ['L' => 'Laki-laki', 'P' => 'Perempuan', 'U' => 'Unisex'][$item->gender] ?? $item->gender ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->type?->label ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Departemen</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->department?->label ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->unit }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga Jual</p>
                    <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($item->selling_price, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">HPP</p>
                    <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($item->hpp, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Variants --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6" x-data="{ showModal: false, selectedSize: '', generatedSku: '', baseCode: '{{ $item->base_code ?? $item->code }}' }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Varian Ukuran</h3>
                <button type="button" @click="showModal = true"
                        class="inline-flex items-center px-4 py-2 bg-primary-700 text-white text-xs font-semibold rounded-lg hover:bg-primary-800 transition shadow-sm">
                    + Tambah Varian
                </button>
            </div>

            {{-- Add Variant Modal --}}
            <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="modal_size_id" :value="__('Size')" class="mb-1"/>
                                    <select name="size_id" id="modal_size_id" required
                                            @change="const opt = $event.target.options[$event.target.selectedIndex]; selectedSize = opt.dataset.code || ''; generatedSku = selectedSize ? (baseCode + '-' + selectedSize) : '';"
                                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 text-sm">
                                        <option value="">-- Pilih Ukuran --</option>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}" data-code="{{ $size->code }}">{{ $size->code }} - {{ $size->label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="modal_size" :value="__('Size Label')" class="mb-1"/>
                                    <x-text-input type="text" name="size" id="modal_size" required x-model="selectedSize" placeholder="S, M, L, XL, 40, 42" class="w-full text-sm"/>
                                </div>
                                <div>
                                    <x-input-label for="modal_sku" :value="__('SKU (opsional)')" class="mb-1"/>
                                    <x-text-input type="text" name="sku" id="modal_sku" x-model="generatedSku" placeholder="Kosongkan untuk otomatis" class="w-full text-sm font-mono"/>
                                </div>
                                <div>
                                    <x-input-label for="modal_weight" :value="__('Berat (opsional, kg)')" class="mb-1"/>
                                    <x-text-input type="number" name="weight" id="modal_weight" min="0" step="0.01" placeholder="0" class="w-full text-sm"/>
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

            @if($item->variants->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ukuran</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Berat</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($item->variants as $variant)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $variant->size_label ?? $variant->size }}</td>
                                    <td class="px-4 py-3 text-sm font-mono text-gray-600">{{ $variant->sku }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 text-right">{{ $variant->weight ? $variant->weight . ' kg' : '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div x-data="{ open: false }">
                                            <button type="button" @click="open = true"
                                                    class="inline-flex items-center justify-center p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Hapus Varian">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>

                                            <div x-show="open" x-cloak
                                                 x-transition:enter="transition ease-out duration-300"
                                                 x-transition:enter-start="opacity-0"
                                                 x-transition:enter-end="opacity-100"
                                                 x-transition:leave="transition ease-in duration-200"
                                                 x-transition:leave-start="opacity-100"
                                                 x-transition:leave-end="opacity-0"
                                                 class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                                <div @click="open = false" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
                                                <div class="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4" @click.outside="open = false">
                                                    <div class="mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                                                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Hapus Varian</h3>
                                                    <p class="text-sm text-gray-500 text-center mb-6">Yakin ingin menghapus varian {{ $variant->size }}? Tindakan ini tidak bisa dibatalkan.</p>
                                                    <div class="flex gap-3 justify-center">
                                                        <form action="{{ route('master-data.item.variant.destroy', [$item, $variant]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                                Ya, Hapus
                                                            </button>
                                                        </form>
                                                        <button type="button" @click="open = false"
                                                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                            Batal
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-400 py-4 text-center">Belum ada varian.</p>
            @endif
        </div>

        {{-- Stok per Ukuran --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Stok per Ukuran</h3>

            @php
                $totalAllStock = 0;
            @endphp

            @if($item->variants->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ukuran</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Reserved</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($item->variants as $variant)
                                @php
                                    $balance = $item->stockBalances->firstWhere('variant_id', $variant->id);
                                    $qty = $balance?->quantity ?? 0;
                                    $reserved = $balance?->reserved ?? 0;
                                    $totalAllStock += $qty;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $variant->size_label ?? $variant->size }}</td>
                                    <td class="px-4 py-3 text-sm font-mono text-gray-600">{{ $variant->sku }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold {{ $qty <= 0 ? 'text-red-600' : ($qty <= 5 ? 'text-amber-600' : 'text-gray-800') }}">
                                        {{ number_format($qty) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-500">
                                        {{ $reserved > 0 ? number_format($reserved) : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-sm font-semibold text-gray-700">Total</td>
                                <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right">{{ number_format($totalAllStock) }}</td>
                                <td class="px-4 py-3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-400 py-4 text-center">Tidak ada data stok. Tambahkan varian terlebih dahulu.</p>
            @endif
        </div>

    </div>
</x-app-layout>
