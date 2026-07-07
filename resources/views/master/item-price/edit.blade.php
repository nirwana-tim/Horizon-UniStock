<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Item Price') }}</h2>
            <a href="{{ route('master-data.item-price.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                {{ __('â† Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('master-data.item-price.update', $itemPrice) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="item_id" :value="__('Item')" />
                                @php
                                    $itemOptions = $items->map(fn($item) => [
                                        'value' => $item->id,
                                        'label' => $item->code . ' - ' . $item->name,
                                        'group' => $item->category->name ?? '',
                                    ])->toArray();
                                @endphp
                                <x-searchable-select name="item_id" :options="$itemOptions" :value="old('item_id', $itemPrice->item_id)" placeholder="-- Select Item --" :required="true" />
                                <x-input-error :messages="$errors->get('item_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="effective_date" :value="__('Effective Date')" />
                                <x-text-input id="effective_date" name="effective_date" type="date" class="mt-1 block w-full" :value="old('effective_date', $itemPrice->effective_date?->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('effective_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="selling_price" :value="__('Selling Price (Rp)')" />
                                <x-text-input id="selling_price" name="selling_price" type="number" class="mt-1 block w-full" :value="old('selling_price', $itemPrice->selling_price)" min="0" step="100" required />
                                <x-input-error :messages="$errors->get('selling_price')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="hpp" :value="__('COGS (Rp)')" />
                                <x-text-input id="hpp" name="hpp" type="number" class="mt-1 block w-full" :value="old('hpp', $itemPrice->hpp)" min="0" step="100" required />
                                <x-input-error :messages="$errors->get('hpp')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                            <a href="{{ route('master-data.item-price.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
