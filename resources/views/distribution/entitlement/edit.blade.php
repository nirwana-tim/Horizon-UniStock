<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Entitlement') }}</h2>
            <a href="{{ route('distribution.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('← Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('distribution.entitlement.update', $entitlement) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="code" :value="__('Kode Entitlement')" />
                                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $entitlement->code)" required />
                                <p class="mt-1 text-xs text-gray-500">Format: {LevelAngkatan}{Fakultas}{Prodi} — otomatis sama dengan kode pada mahasiswa</p>
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="is_active" :value="__('Status')" />
                                <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="1" {{ old('is_active', $entitlement->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $entitlement->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Deskripsi')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" rows="2">{{ old('description', $entitlement->description) }}</textarea>
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

                                    $entitlementItems = old('items', $entitlement->items->map(fn($i) => ['item_id' => $i->item_id, 'quantity' => $i->quantity])->toArray());
                                @endphp

                                <div id="items-container" class="mt-2 space-y-2">
                                    @forelse($entitlementItems as $idx => $ei)
                                        <div class="item-row flex items-center gap-2 p-2 border rounded bg-gray-50">
                                            <div class="flex-1">
                                                <x-searchable-select name="items[{{ $idx }}][item_id]" :options="$itemOptions" :value="$ei['item_id']" placeholder="-- Pilih Item --" :required="true" />
                                            </div>
                                            <x-text-input name="items[{{ $idx }}][quantity]" type="number" class="w-20" min="1" :value="$ei['quantity'] ?? 1" required />
                                            <button type="button" class="remove-item px-2 py-1 text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                                        </div>
                                    @empty
                                        <div class="item-row flex items-center gap-2 p-2 border rounded bg-gray-50">
                                            <div class="flex-1">
                                                <x-searchable-select name="items[0][item_id]" :options="$itemOptions" placeholder="-- Pilih Item --" :required="true" />
                                            </div>
                                            <x-text-input name="items[0][quantity]" type="number" class="w-20" min="1" value="1" required />
                                            <button type="button" class="remove-item px-2 py-1 text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                                        </div>
                                    @endforelse
                                </div>
                                <button type="button" id="add-item" class="mt-2 inline-flex items-center px-3 py-1 bg-primary-100 border border-transparent rounded-md font-semibold text-xs text-primary-700 uppercase tracking-widest hover:bg-primary-200 transition">+ Tambah Item</button>
                                <x-input-error :messages="$errors->get('items')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>{{ __('Perbarui') }}</x-primary-button>
                            <a href="{{ route('distribution.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('Batal') }}</a>
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
        let itemIndex = {{ count($entitlementItems) }};
        const itemOptions = @json($itemOptions);

        function createItemRow(selectedValue = '', quantity = 1) {
            const div = document.createElement('div');
            div.className = 'item-row flex items-center gap-2 p-2 border rounded bg-gray-50';

            const selectWrapper = document.createElement('div');
            selectWrapper.className = 'flex-1';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `items[${itemIndex}][item_id]`;
            input.value = selectedValue;
            input.required = true;

            selectWrapper.innerHTML = `
                <div x-data="{
                    open: false,
                    search: '',
                    selectedValue: '${selectedValue}',
                    selectedLabel: '',
                    highlightedIndex: -1,
                    get filteredOptions() {
                        const q = this.search.toLowerCase();
                        return ${JSON.stringify(itemOptions)}.filter(opt =>
                            opt.label.toLowerCase().includes(q) || (opt.group && opt.group.toLowerCase().includes(q))
                        );
                    },
                    select(value, label) {
                        this.selectedValue = value;
                        this.selectedLabel = label;
                        this.open = false;
                        this.search = '';
                        this.highlightedIndex = -1;
                        this.$refs.hiddenInput.value = value;
                    },
                    handleKeydown(e) {
                        if (e.key === 'Escape') { this.open = false; return; }
                        if (e.key === 'ArrowDown') { e.preventDefault(); this.highlightedIndex = Math.min(this.highlightedIndex + 1, this.filteredOptions.length - 1); }
                        else if (e.key === 'ArrowUp') { e.preventDefault(); this.highlightedIndex = Math.max(this.highlightedIndex - 1, 0); }
                        else if (e.key === 'Enter') {
                            e.preventDefault();
                            if (this.highlightedIndex >= 0 && this.filteredOptions[this.highlightedIndex]) {
                                const opt = this.filteredOptions[this.highlightedIndex];
                                this.select(opt.value, opt.label);
                            }
                        }
                    }
                }" class="relative">
                    <input type="hidden" x-ref="hiddenInput" name="items[${itemIndex}][item_id]" :value="selectedValue" required />
                    <div @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
                         class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm cursor-pointer bg-white flex items-center justify-between px-3 py-2"
                         :class="open ? 'ring-2 ring-primary-500 border-primary-500' : ''">
                        <span x-text="selectedLabel || '-- Pilih Item --'" :class="selectedLabel ? 'text-gray-900' : 'text-gray-400'"></span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div x-show="open" x-cloak @click.outside="open = false"
                         class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-hidden">
                        <div class="sticky top-0 bg-white p-2 border-b border-gray-100">
                            <input x-ref="searchInput" x-model="search" @keydown="handleKeydown($event)" type="text"
                                   placeholder="Ketik untuk mencari..."
                                   class="w-full px-3 py-1.5 text-sm border border-gray-200 rounded-md focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none" />
                        </div>
                        <ul class="overflow-y-auto max-h-48 py-1">
                            <template x-for="(opt, index) in filteredOptions" :key="opt.value">
                                <li @click="select(opt.value, opt.label)" @mouseenter="highlightedIndex = index"
                                    class="px-3 py-2 text-sm cursor-pointer flex items-center justify-between"
                                    :class="{ 'bg-primary-50 text-primary-700': highlightedIndex === index, 'hover:bg-gray-50 text-gray-900': highlightedIndex !== index }">
                                    <span x-text="opt.label"></span>
                                    <span x-show="opt.group" x-text="opt.group" class="text-xs text-gray-400 ml-2"></span>
                                </li>
                            </template>
                            <li x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-400 italic text-center">Tidak ditemukan</li>
                        </ul>
                    </div>
                </div>
            `;

            selectWrapper.appendChild(input);

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

            div.appendChild(selectWrapper);
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
