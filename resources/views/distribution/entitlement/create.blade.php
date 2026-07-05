<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Entitlement') }}</h2>
            <a href="{{ route('distribution.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('← Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('distribution.entitlement.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="code" :value="__('Kode Entitlement')" />
                                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code')" placeholder="Contoh: 2425FHSS1-KEP" required />
                                <p class="mt-1 text-xs text-gray-500">Format: {LevelAngkatan}{Fakultas}{Prodi} — otomatis sama dengan kode pada mahasiswa</p>
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="is_active" :value="__('Status')" />
                                <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Deskripsi')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" rows="2">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label :value="__('Item & Jumlah')" />
                                <p class="mt-1 mb-2 text-xs text-gray-500">Pilih item (produk) dan jumlah yang diberikan. Ukuran akan dipilih oleh mahasiswa saat login.</p>

                                @php
                                    $itemOptions = $items->map(fn($it) => [
                                        'value' => $it->id,
                                        'label' => $it->name . ' (' . $it->code . ')',
                                        'group' => $it->sizes->pluck('label')->implode(', '),
                                    ])->toArray();
                                @endphp

                                <div id="items-container" class="mt-2 space-y-2">
                                    @if(old('items'))
                                        @foreach(old('items') as $idx => $item)
                                            <div class="item-row flex items-center gap-2 p-2 border rounded bg-gray-50">
                                                <div class="flex-1">
                                                    <x-searchable-select name="items[{{ $idx }}][item_id]" :options="$itemOptions" :value="$item['item_id']" placeholder="-- Pilih Item --" :required="true" />
                                                </div>
                                                <x-text-input name="items[{{ $idx }}][quantity]" type="number" class="w-20" min="1" :value="$item['quantity']" required />
                                                <button type="button" class="remove-item px-2 py-1 text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="item-row flex items-center gap-2 p-2 border rounded bg-gray-50">
                                            <div class="flex-1">
                                                <x-searchable-select name="items[0][item_id]" :options="$itemOptions" placeholder="-- Pilih Item --" :required="true" />
                                            </div>
                                            <x-text-input name="items[0][quantity]" type="number" class="w-20" min="1" value="1" required />
                                            <button type="button" class="remove-item px-2 py-1 text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" id="add-item" class="mt-2 inline-flex items-center px-3 py-1 bg-primary-100 border border-transparent rounded-md font-semibold text-xs text-primary-700 uppercase tracking-widest hover:bg-primary-200 transition">+ Tambah Item</button>
                                <x-input-error :messages="$errors->get('items')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            <a href="{{ route('distribution.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('Batal') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = {{ old('items') ? count(old('items')) : 1 }};
        const itemOptions = @json($itemOptions);

        function createItemRow(selectedValue = '', quantity = 1) {
            const div = document.createElement('div');
            div.className = 'item-row flex items-center gap-2 p-2 border rounded bg-gray-50';

            const select = document.createElement('select');
            select.name = `items[${itemIndex}][item_id]`;
            select.className = 'flex-1 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm';
            select.required = true;

            const emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.textContent = '-- Pilih Item --';
            select.appendChild(emptyOpt);

            itemOptions.forEach(function(opt) {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.label;
                select.appendChild(option);
            });

            select.value = selectedValue;

            const qtyInput = document.createElement('input');
            qtyInput.type = 'number';
            qtyInput.name = `items[${itemIndex}][quantity]`;
            qtyInput.className = 'w-20 mt-1 block border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm';
            qtyInput.min = '1';
            qtyInput.value = quantity;
            qtyInput.required = true;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-item px-2 py-1 text-red-600 hover:text-red-800 text-sm font-medium';
            removeBtn.textContent = 'Hapus';

            div.appendChild(select);
            div.appendChild(qtyInput);
            div.appendChild(removeBtn);

            itemIndex++;
            return div;
        }

        document.getElementById('add-item').addEventListener('click', function () {
            const container = document.getElementById('items-container');
            container.appendChild(createItemRow());
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
</x-app-layout>
