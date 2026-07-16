@forelse($data as $type)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data->firstItem() + $loop->index }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $type->code }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $type->label }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm">
            @if($type->categories->count())
                <div class="flex flex-wrap gap-1">
                    @foreach($type->categories as $cat)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                            {{ $cat->label }} ({{ $cat->code }})
                        </span>
                    @endforeach
                </div>
            @else
                <span class="text-xs text-gray-400 italic">Not connected</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1">
            <a href="{{ route('master-data.item-type.show', $type) }}" class="inline-flex items-center justify-center p-1.5 text-primary-600 hover:text-primary-800 hover:bg-primary-50 rounded-lg transition-colors" title="Lihat Detail">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </a>
            <x-delete-modal
                :route="route('master-data.item-type.destroy', $type)"
                label="Delete Item Type"
                description="Are you sure you want to delete this item type? This action cannot be undone."
                :iconOnly="true"
            />
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No data found.</td>
    </tr>
@endforelse

