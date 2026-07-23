@forelse($entitlements as $entitlement)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration + ($entitlements->currentPage() - 1) * $entitlements->perPage() }}</td>
        <td class="px-6 py-4 whitespace-nowrap"><a href="{{ route('distribution.entitlement.show', $entitlement) }}" class="text-sm font-medium text-primary-600 hover:text-primary-900 font-mono">{{ $entitlement->code }}</a></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $entitlement->description ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap"><x-badge type="{{ $entitlement->student_level === 'Y1S1' ? 'info' : 'warning' }}">{{ $entitlement->student_level_label }}</x-badge></td>
        <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $entitlement->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $entitlement->is_active ? 'Active' : 'Inactive' }}</span></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $entitlement->items->count() }} item</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1">
            <a href="{{ route('distribution.entitlement.show', $entitlement) }}" class="inline-flex items-center justify-center p-1.5 text-primary-600 hover:text-primary-800 hover:bg-primary-50 rounded-lg transition-colors" title="Lihat Detail">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </a>
            <x-delete-modal
                :route="route('distribution.entitlement.destroy', $entitlement)"
                label="Delete Entitlement"
                description="Are you sure you want to delete entitlement {{ $entitlement->code }}? This data cannot be restored."
                :iconOnly="true"
            />
        </td>
    </tr>
@empty
    <tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No entitlement data found.') }}</td></tr>
@endforelse

