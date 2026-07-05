<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Penerimaan Barang Baru') }}</h2>
            <a href="{{ route('inventory.stock-receive.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('â† Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('inventory.stock-receive.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="vendor_id" :value="__('Vendor')" />
                                @php
                                    $vendorOptions = $vendors->map(fn($v) => [
                                        'value' => $v->id,
                                        'label' => $v->name,
                                        'group' => '',
                                    ])->toArray();
                                @endphp
                                <x-searchable-select name="vendor_id" :options="$vendorOptions" :value="old('vendor_id')" placeholder="-- Pilih Vendor --" :required="true" />
                                <x-input-error :messages="$errors->get('vendor_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="receive_date" :value="__('Tanggal Terima')" />
                                <x-text-input id="receive_date" name="receive_date" type="date" class="mt-1 block w-full" :value="old('receive_date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('receive_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="reference_number" :value="__('No. Referensi')" />
                                <x-text-input id="reference_number" name="reference_number" type="text" class="mt-1 block w-full" :value="old('reference_number')" placeholder="Kosongkan untuk generate otomatis" />
                                <x-input-error :messages="$errors->get('reference_number')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="notes" :value="__('Catatan')" />
                                <textarea id="notes" name="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" rows="2">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-input-label :value="__('Item Barang')" />
                            <div class="mt-2 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200" id="items-table">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Varian</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Qty</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Harga Satuan</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">HPP</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-container" class="bg-white divide-y divide-gray-200">
                                        @php
                                            $itemOptions = $items->map(fn($item) => [
                                                'value' => $item->id,
                                                'label' => $item->name . ' (' . ($item->category?->code ?? '-') . ')',
                                                'group' => $item->base_code,
                                            ])->toArray();
                                        @endphp
                                        <tr class="item-row hover:bg-gray-50">
                                            <td class="px-4 py-2">
                                                <x-searchable-select name="items[0][item_id]" :options="$itemOptions" placeholder="-- Pilih Item --" :required="true" />
                                            </td>
                                            <td class="px-4 py-2">
                                                @php
                                                    $variantOptions = collect();
                                                    foreach ($items as $item) {
                                                        foreach ($item->variants as $variant) {
                                                            $variantOptions->push([
                                                                'value' => $variant->id,
                                                                'label' => $item->name . ' - ' . $variant->size_label . ' (' . $variant->sku . ')',
                                                                'group' => $item->base_code,
                                                            ]);
                                                        }
                                                    }
                                                @endphp
                                                <x-searchable-select name="items[0][variant_id]" :options="$variantOptions->toArray()" placeholder="-- Pilih Varian --" :required="true" />
                                            </td>
                                            <td class="px-4 py-2">
                                                <x-text-input name="items[0][quantity]" type="number" class="w-full" min="1" value="1" required />
                                            </td>
                                            <td class="px-4 py-2">
                                                <x-text-input name="items[0][unit_price]" type="number" class="w-full" min="0" value="0" placeholder="0" />
                                            </td>
                                            <td class="px-4 py-2">
                                                <x-text-input name="items[0][hpp]" type="number" class="w-full" min="0" value="0" placeholder="0" />
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <button type="button" class="remove-item text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" id="add-item" class="mt-2 inline-flex items-center px-3 py-1 bg-primary-100 border border-transparent rounded-md font-semibold text-xs text-primary-700 uppercase tracking-widest hover:bg-primary-200 focus:bg-primary-200 active:bg-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">+ Tambah Item</button>
                            <x-input-error :messages="$errors->get('items')" class="mt-2" />
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            <a href="{{ route('inventory.stock-receive.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Batal') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = {{ old('items') ? count(old('items')) : 1 }};

        function filterVariants(select) {
            const row = select.closest('.item-row');
            const itemId = select.value;
            const variantSelect = row.querySelector('.variant-select');

            variantSelect.querySelectorAll('option').forEach(function (opt) {
                if (opt.value === '') return;
                if (opt.dataset.itemId === itemId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });
            variantSelect.value = '';
        }

        document.querySelectorAll('.item-select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                filterVariants(this);
            });
            filterVariants(sel);
        });

        document.getElementById('add-item').addEventListener('click', function () {
            const container = document.getElementById('items-container');
            const template = container.querySelector('.item-row');
            const newRow = template.cloneNode(true);

            newRow.querySelectorAll('select, input').forEach(function (el) {
                const name = el.getAttribute('name');
                if (name) {
                    el.setAttribute('name', name.replace(/\d+/, itemIndex));
                }
                if (el.tagName === 'SELECT') {
                    el.selectedIndex = 0;
                } else {
                    el.value = '';
                }
            });

            const itemSelect = newRow.querySelector('.item-select');
            itemSelect.addEventListener('change', function () {
                filterVariants(this);
            });

            container.appendChild(newRow);
            itemIndex++;
        });

        document.getElementById('items-container').addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-item')) {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    e.target.closest('.item-row').remove();
                }
            }
        });
    });
</script>
@endpush
