@if($lowStockItems->count())
<div class="bg-white rounded-xl border border-amber-200 shadow-sm mb-6 overflow-hidden">
    <div class="flex items-center gap-3 px-5 py-3.5 border-b border-amber-100 bg-amber-50">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <h3 class="text-sm font-semibold text-amber-800">Low Stock Warning</h3>
            <p class="text-xs text-amber-600">{{ $lowStockItems->count() }} item(s) need attention</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Remaining Stock</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($lowStockItems as $balance)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3 text-gray-800 font-medium">{{ $balance->item?->name ?? '-' }}</td>
                    <td class="px-5 py-3 text-right font-bold {{ $balance->quantity <= 2 ? 'text-red-600' : 'text-amber-600' }}">
                        {{ $balance->quantity }}
                    </td>
                    <td class="px-5 py-3 text-right">
                        @if($balance->quantity <= 2)
                            <x-badge type="danger">Critical</x-badge>
                        @else
                            <x-badge type="warning">Low</x-badge>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif