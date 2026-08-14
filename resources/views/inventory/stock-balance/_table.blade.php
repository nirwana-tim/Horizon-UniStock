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
            @php
                $demand = \Illuminate\Support\Facades\DB::table('student_size_items')
                    ->join('student_size_profiles', 'student_size_items.size_profile_id', '=', 'student_size_profiles.id')
                    ->where('student_size_items.item_id', $balance->item_id)
                    ->where('student_size_items.size', $balance->variant?->size)
                    ->count();
                $qty = $balance->quantity;
            @endphp
            @if($qty <= 0 && $demand > 0)
                <x-badge type="danger">Out of Stock</x-badge>
            @elseif($demand > 0 && $qty < $demand)
                <x-badge type="warning">Kurang {{ $demand - $qty }}</x-badge>
            @elseif($demand > 0 && $qty >= $demand)
                <x-badge type="success">Cukup</x-badge>
            @else
                <x-badge type="info">Tanpa Demand</x-badge>
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
