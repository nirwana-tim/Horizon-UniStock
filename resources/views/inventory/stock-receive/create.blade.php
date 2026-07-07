<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('New Stock Receive') }}</h2>
            <a href="{{ route('inventory.stock-receive.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('← Back') }}</a>
        </div>
    </x-slot>

    @php
        $itemOptions = $items->map(fn($item) => [
            'value' => $item->id,
            'label' => $item->name . ' (' . ($item->category?->code ?? '-') . ')',
        ])->toArray();

        $variantOptions = collect();
        foreach ($items as $item) {
            foreach ($item->variants as $variant) {
                $variantOptions->push([
                    'value' => $variant->id,
                    'item_id' => $item->id,
                    'label' => $item->name . ' - ' . $variant->size_label . ' (' . $variant->sku . ')',
                ]);
            }
        }
    @endphp

    <div class="py-12" x-data="{
        items: {{ json_encode(old('items', [])) }},
        showModal: false,
        newItem: { item_id: '', variant_id: '', quantity: 1, unit_price: 0, hpp: 0 },
        itemOptions: {{ json_encode($itemOptions) }},
        variantOptions: {{ json_encode($variantOptions) }},
        
        get filteredVariantOptions() {
            if (!this.newItem.item_id) return [];
            return this.variantOptions.filter(v => v.item_id == this.newItem.item_id);
        },
        
        addItem() {
            if (!this.newItem.item_id || !this.newItem.variant_id) {
                alert('Please select an item and variant first.');
                return;
            }
            
            const itemOpt = this.itemOptions.find(o => o.value == this.newItem.item_id);
            const varOpt = this.variantOptions.find(o => o.value == this.newItem.variant_id);
            
            this.items.push({
                item_id: this.newItem.item_id,
                item_label: itemOpt ? itemOpt.label : '',
                variant_id: this.newItem.variant_id,
                variant_label: varOpt ? varOpt.label : '',
                quantity: this.newItem.quantity,
                unit_price: this.newItem.unit_price,
                hpp: this.newItem.hpp
            });
            
            this.newItem = { item_id: '', variant_id: '', quantity: 1, unit_price: 0, hpp: 0 };
            this.showModal = false;
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
        },
        
        init() {
            this.items = this.items.map(item => {
                const itemOpt = this.itemOptions.find(o => o.value == item.item_id);
                const varOpt = this.variantOptions.find(o => o.value == item.variant_id);
                return {
                    ...item,
                    item_label: itemOpt ? itemOpt.label : '',
                    variant_label: varOpt ? varOpt.label : (item.variant_label || '')
                };
            });
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('inventory.stock-receive.store') }}" method="POST">
                        @csrf

                        <template x-for="(item, index) in items" :key="index">
                            <div>
                                <input type="hidden" :name="'items['+index+'][item_id]'" :value="item.item_id">
                                <input type="hidden" :name="'items['+index+'][variant_id]'" :value="item.variant_id">
                                <input type="hidden" :name="'items['+index+'][quantity]'" :value="item.quantity">
                                <input type="hidden" :name="'items['+index+'][unit_price]'" :value="item.unit_price">
                                <input type="hidden" :name="'items['+index+'][hpp]'" :value="item.hpp">
                            </div>
                        </template>

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
                                <x-searchable-select name="vendor_id" :options="$vendorOptions" :value="old('vendor_id')" placeholder="-- Select Vendor --" :required="true" />
                                <x-input-error :messages="$errors->get('vendor_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="receive_date" :value="__('Receive Date')" />
                                <x-text-input id="receive_date" name="receive_date" type="date" class="mt-1 block w-full" :value="old('receive_date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('receive_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="reference_number" :value="__('Reference No.')" />
                                <x-text-input id="reference_number" name="reference_number" type="text" class="mt-1 block w-full" :value="old('reference_number')" placeholder="Leave blank for auto-generate" />
                                <x-input-error :messages="$errors->get('reference_number')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="notes" :value="__('Notes')" />
                                <textarea id="notes" name="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" rows="2">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 border-t pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <x-input-label :value="__('Received Items List')" class="text-base font-semibold" />
                                <button type="button" @click="showModal = true" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 transition shadow-sm">+ Add Item</button>
                            </div>

                            <div class="overflow-x-auto bg-gray-50 rounded-xl border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100/70">
                                        <tr>
                                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Variant</th>
                                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Qty</th>
<th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-36">Unit Price</th>
                                             <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-36">HPP</th>
                                            <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr class="hover:bg-gray-50/50">
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900" x-text="item.item_label"></td>
                                                <td class="px-6 py-4 text-sm text-gray-500" x-text="item.variant_label"></td>
                                                <td class="px-6 py-4 text-sm text-gray-900 font-semibold" x-text="item.quantity"></td>
                                                <td class="px-6 py-4 text-sm text-gray-500" x-text="'Rp ' + Number(item.unit_price).toLocaleString('id-ID')"></td>
                                                <td class="px-6 py-4 text-sm text-gray-500" x-text="'Rp ' + Number(item.hpp).toLocaleString('id-ID')"></td>
                                                <td class="px-6 py-4 text-sm text-center">
                                                    <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-800 font-semibold transition">Delete</button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="items.length === 0">
                                            <td colspan="6" class="px-6 py-10 text-sm text-gray-400 italic text-center">
                                                No items added yet. Please click "+ Add Item".
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <x-input-error :messages="$errors->get('items')" class="mt-2" />
                        </div>

                        <div class="mt-8 flex items-center gap-3 border-t pt-6">
                            <x-primary-button>{{ __('Save Receive') }}</x-primary-button>
                            <a href="{{ route('inventory.stock-receive.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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
                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6"
                     @change="
                        if ($event.target.name === 'modal_item_id') {
                            newItem.item_id = $event.target.value;
                            newItem.variant_id = '';
                        }
                     ">
                    
                    <div class="flex items-center justify-between border-b pb-3 mb-4">
                        <h3 class="text-base font-bold text-gray-900">Add Item</h3>
                        <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-500 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <x-input-label :value="__('Select Item')" class="mb-1" />
                            <x-searchable-select name="modal_item_id" :options="$itemOptions" placeholder="-- Search Item --" />
                        </div>

                        <div>
                            <x-input-label :value="__('Select Size Variant')" class="mb-1" />
                            <select x-model="newItem.variant_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                <option value="">-- Select Size Variant --</option>
                                <template x-for="opt in filteredVariantOptions" :key="opt.value">
                                    <option :value="opt.value" x-text="opt.label"></option>
                                </template>
                            </select>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <x-input-label :value="__('Quantity')" class="mb-1" />
                                <x-text-input type="number" x-model="newItem.quantity" min="1" class="w-full text-sm" required />
                            </div>
                            <div>
                                <x-input-label :value="__('Unit Price')" class="mb-1" />
                                <x-text-input type="number" x-model="newItem.unit_price" min="0" class="w-full text-sm" />
                            </div>
                            <div>
                                <x-input-label :value="__('HPP')" class="mb-1" />
                                <x-text-input type="number" x-model="newItem.hpp" min="0" class="w-full text-sm" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2 border-t pt-4">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">Cancel</button>
                        <button type="button" @click="addItem()" class="px-4 py-2 bg-primary-700 hover:bg-primary-800 text-white text-xs font-semibold rounded-lg transition shadow-sm">Add to List</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
