<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Entitlement') }}</h2>
            <a href="{{ route('distribution.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('← Back') }}</a>
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
                                <x-input-label :value="__('Entitlement Code (Read-Only)') />
                                <x-text-input id="code_display" type="text" class="mt-1 block w-full bg-gray-50 text-gray-500 font-mono" :value="$entitlement->code" disabled />
                                <input type="hidden" name="code" value="{{ $entitlement->code }}">
                            </div>
                            <div>
                                <x-input-label for="is_active" :value="__('Status')" />
                                <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="1" {{ old('is_active', $entitlement->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $entitlement->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" rows="2">{{ old('description', $entitlement->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            
                            {{-- Simplified Grid of Checked Items --}}
                            <div class="md:col-span-2">
                                <x-input-label :value="__('Select Items & Entitlement Quantity')" />
                                <p class="mt-1 mb-4 text-xs text-gray-500">Select items that students are entitled to and adjust the quantity.</p>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($items as $idx => $item)
                                        @php
                                            $oldItem = collect(old('items', $entitlement->items))->first(fn($i) => ($i['item_id'] ?? $i->item_id ?? null) == $item->id);
                                            $isChecked = !empty($oldItem);
                                            $qty = $oldItem['quantity'] ?? $oldItem->quantity ?? 1;
                                        @endphp
                                        <div class="flex items-center justify-between p-3 border rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                                            <label class="flex items-center space-x-2 cursor-pointer flex-1 mr-2">
                                                <input type="checkbox" 
                                                       name="items[{{ $idx }}][checked]" 
                                                       value="1" 
                                                       {{ $isChecked ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-primary-700 shadow-sm focus:ring-primary-500">
                                                <span class="text-sm text-gray-700 font-semibold">{{ $item->name }} ({{ $item->code }})</span>
                                            </label>
                                            
                                            <input type="hidden" name="items[{{ $idx }}][item_id]" value="{{ $item->id }}">
                                            
                                            <div class="flex items-center gap-1">
                                                <span class="text-xs text-gray-500">Qty:</span>
                                                <input type="number" 
                                                       name="items[{{ $idx }}][quantity]" 
                                                       value="{{ $qty }}" 
                                                       min="1" 
                                                       class="w-16 rounded-md border-gray-300 py-1 px-2 text-sm focus:border-primary-500 focus:ring-primary-500">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('items')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>{{ __('Update') }}</x-primary-button>
                            <a href="{{ route('distribution.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
