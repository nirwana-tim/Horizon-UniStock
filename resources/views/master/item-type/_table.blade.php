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
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
              <a href="{{ route('master-data.item-type.show', $type) }}" class="inline-flex items-center px-2.5 py-1 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">View</a>
            <x-delete-modal
                :route="route('master-data.item-type.destroy', $type)"
                label="Delete Item Type"
                description="Are you sure you want to delete this item type? This action cannot be undone."
            />
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No data found.</td>
    </tr>
@endforelse

