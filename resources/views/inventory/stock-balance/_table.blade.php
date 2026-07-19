@forelse($balances as $balance)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration + ($balances->currentPage() - 1) * $balances->perPage() }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $balance->item?->name ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $balance->item?->code ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $balance->item?->category?->label ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $balance->variant?->size_label ?? 'All Size' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $balance->variant?->sku ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right tabular-nums {{ $balance->quantity > 0 ? 'text-gray-900' : 'text-red-600 font-medium' }}">{{ number_format($balance->quantity) }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right tabular-nums text-gray-500">Rp {{ number_format($balance->last_hpp ?? 0, 0, ',', '.') }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right tabular-nums text-gray-900 font-medium">Rp {{ number_format(($balance->quantity * ($balance->last_hpp ?? 0)), 0, ',', '.') }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-center">
            @if($balance->quantity <= 0)
                <x-badge type="danger">Out of Stock</x-badge>
            @elseif($balance->quantity <= 10)
                <x-badge type="warning">Low Stock</x-badge>
            @else
                <x-badge type="success">In Stock</x-badge>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="px-6 py-10">
            <x-empty-state title="No Stock Balance" description="No stock data found. Receive stock first." />
        </td>
    </tr>
@endforelse
