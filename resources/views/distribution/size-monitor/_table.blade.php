@forelse($histories as $index => $history)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $histories->firstItem() + $index }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $history->sizeItem?->sizeProfile?->student?->name ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $history->sizeItem?->sizeProfile?->student?->nim ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $history->sizeItem?->item?->name ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $history->old_size ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-primary-600">{{ $history->new_size }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $history->changedByUser?->name ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $history->changed_at?->format('d/m/Y H:i') ?? '-' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No size change history found.</td>
    </tr>
@endforelse

