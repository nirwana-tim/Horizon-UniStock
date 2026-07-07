<x-input-label :value="__('Select Items & Entitlement Quantity')" />
<p class="mt-1 mb-4 text-xs text-gray-500">Select items that students are entitled to and adjust the quantity.</p>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
    @foreach($items as $idx => $item)
        @php
            $existingItems = $entitlement?->items ?? collect(old('items', []));
            $oldItem = $existingItems->first(fn($i) => ($i['item_id'] ?? $i->item_id ?? null) == $item->id);
            $isChecked = !empty($oldItem);
            $qty = $isChecked ? ($oldItem['quantity'] ?? $oldItem->quantity ?? 1) : 1;
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