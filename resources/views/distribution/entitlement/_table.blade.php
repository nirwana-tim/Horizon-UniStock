@forelse($entitlements as $entitlement)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration + ($entitlements->currentPage() - 1) * $entitlements->perPage() }}</td>
        <td class="px-6 py-4 whitespace-nowrap"><a href="{{ route('distribution.entitlement.show', $entitlement) }}" class="text-sm font-medium text-primary-600 hover:text-primary-900 font-mono">{{ $entitlement->code }}</a></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $entitlement->description ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $entitlement->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $entitlement->is_active ? 'Active' : 'Inactive' }}</span></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $entitlement->items->count() }} item</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
              <a href="{{ route('distribution.entitlement.show', $entitlement) }}" class="inline-flex items-center px-2.5 py-1 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">View</a>
            <x-delete-modal
                :route="route('distribution.entitlement.destroy', $entitlement)"
                label="Delete Entitlement"
                description="Are you sure you want to delete entitlement {{ $entitlement->code }}? This data cannot be restored."
            />
        </td>
    </tr>
@empty
    <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No entitlement data found.') }}</td></tr>
@endforelse

