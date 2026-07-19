@forelse($movements as $movement)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration + ($movements->currentPage() - 1) * $movements->perPage() }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $movement->item?->name ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $movement->variant?->size_label ?? 'All Size' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-center">
            @if($movement->type === 'IN')
                <x-badge type="success">IN</x-badge>
            @else
                <x-badge type="danger">OUT</x-badge>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right tabular-nums {{ $movement->type === 'IN' ? 'text-green-600' : 'text-red-600' }} font-medium">{{ $movement->type === 'IN' ? '+' : '-' }}{{ number_format($movement->quantity) }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right tabular-nums text-gray-500">Rp {{ number_format($movement->hpp ?? 0, 0, ',', '.') }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right tabular-nums text-gray-900">Rp {{ number_format(($movement->quantity * ($movement->hpp ?? 0)), 0, ',', '.') }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-[160px] truncate" title="{{ $movement->reference_type }} #{{ $movement->reference_id }}">
            {{ class_basename($movement->reference_type) }} #{{ $movement->reference_id }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-[200px] truncate" title="{{ $movement->notes }}">{{ $movement->notes ?? '-' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="px-6 py-10">
            <x-empty-state title="No Movements" description="No stock movements recorded yet." />
        </td>
    </tr>
@endforelse
