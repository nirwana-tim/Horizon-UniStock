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
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1">
            <a href="{{ route('master-data.item.show', $variant->item?->code ?? '') }}" class="inline-flex items-center justify-center p-1.5 text-primary-600 hover:text-primary-800 hover:bg-primary-50 rounded-lg transition-colors" title="Lihat Detail">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </a>
            <x-delete-modal
                :route="route('master-data.item.variant.destroy', [$variant->item, $variant])"
                label="Delete Variant"
                description="Are you sure you want to delete variant/SKU {{ $variant->sku }}? This action cannot be undone."
                :iconOnly="true"
            />
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No SKU/Variant data found.</td>
    </tr>
@endforelse

