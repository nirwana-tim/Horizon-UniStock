@forelse($data as $variant)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data->firstItem() + $loop->index }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $variant->sku }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ $variant->item?->name }}, Size {{ $variant->size_label ?? $variant->size }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $variant->item?->category?->label ?? '-' }} ({{ $variant->item?->category?->code ?? '-' }})
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $variant->item?->unit ?? 'pcs' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
             <a href="{{ route('master-data.item.show', $variant->item?->code ?? '') }}" class="inline-flex items-center px-2.5 py-1 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">View</a>
            <x-delete-modal
                :route="route('master-data.item.variant.destroy', [$variant->item, $variant])"
                label="Delete Variant"
                description="Are you sure you want to delete variant/SKU {{ $variant->sku }}? This action cannot be undone."
            />
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No SKU/Variant data found.</td>
    </tr>
@endforelse

