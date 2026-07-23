@forelse($data as $item)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data->firstItem() + $loop->index }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $item->code }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->name }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $item->category?->label ?? '-' }} ({{ $item->category?->code ?? '-' }})
        </td>
        <td class="px-6 py-4 text-sm">
            <div class="flex flex-wrap gap-1">
                @forelse($item->variants as $v)
                    <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs font-medium">
                        {{ $v->size_label ?? $v->size }}
                    </span>
                @empty
                    <span class="text-gray-400 italic">No sizes</span>
                @endforelse
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->unit ?? 'pcs' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1">
            <a href="{{ route('master-data.item.edit', $item) }}"
               class="inline-flex items-center justify-center p-1.5 text-primary-600 hover:text-primary-800 hover:bg-primary-50 rounded-lg transition-colors"
               title="Edit Item">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <x-delete-modal
                :route="route('master-data.item.destroy', $item)"
                label="Delete Item"
                description="Are you sure you want to delete item {{ $item->code }}? All associated variants will also be deleted."
                :iconOnly="true"
            />
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No items found.</td>
    </tr>
@endforelse
