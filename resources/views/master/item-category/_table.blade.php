@forelse($data as $category)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data->firstItem() + $loop->index }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $category->code }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $category->label }}</td>

        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
             <a href="{{ route('master-data.item-category.show', ['item_category' => $category->id]) }}" class="inline-flex items-center px-2.5 py-1 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">View</a>
            <x-delete-modal
                :route="route('master-data.item-category.destroy', ['item_category' => $category->id])"
                label="Delete Item Category"
                description="Are you sure you want to delete this category? This action cannot be undone."
            />
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No data found.</td>
    </tr>
@endforelse

