@php $checkedIds = $checkedIds ?? []; @endphp
@forelse($items as $item)
    <label class="flex items-center space-x-2 p-3 border rounded-lg bg-gray-50 hover:bg-gray-100 transition cursor-pointer">
        <input type="checkbox" name="item_ids[]" value="{{ $item->id }}"
               {{ in_array($item->id, $checkedIds) ? 'checked' : '' }}
               class="rounded border-gray-300 text-primary-700 shadow-sm focus:ring-primary-500">
        <span class="text-sm text-gray-700 font-semibold">{{ $item->name }} ({{ $item->code }})</span>
    </label>
@empty
    <p class="text-sm text-gray-400 col-span-3 text-center py-8">No items available for the selected criteria.</p>
@endforelse
