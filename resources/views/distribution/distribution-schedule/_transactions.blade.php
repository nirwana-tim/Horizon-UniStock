<div class="border-t border-gray-200 pt-6 mt-6"
     x-data="{ openTx: null }">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Transactions') }}</h3>
    @if($transactions->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pickup Time</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transactions as $tx)
                        @php $txId = $tx->id; @endphp
                        <tr class="hover:bg-gray-50 cursor-pointer"
                            @click="openTx = (openTx === {{ $txId }}) ? null : {{ $txId }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tx->student?->name ?? '-' }} ({{ $tx->student?->nim ?? '-' }})</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tx->status === 'completed' ? 'bg-green-100 text-green-800' : ($tx->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ $tx->status }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tx->pickup_time?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-400">
                                <svg x-show="openTx !== {{ $txId }}" class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                <svg x-show="openTx === {{ $txId }}" class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </td>
                        </tr>
                        <tr x-show="openTx === {{ $txId }}" x-cloak>
                            <td colspan="5" class="px-6 py-4 bg-gray-50">
                                <div class="text-xs text-gray-500 mb-2">
                                    @if($tx->notes)
                                        <span class="font-medium">Notes:</span> {{ $tx->notes }}
                                    @endif
                                </div>
                                <table class="min-w-full text-xs">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="py-2 pr-4 text-left font-medium text-gray-500 uppercase">Item</th>
                                            <th class="py-2 pr-4 text-left font-medium text-gray-500 uppercase">Code</th>
                                            <th class="py-2 pr-4 text-left font-medium text-gray-500 uppercase">Size</th>
                                            <th class="py-2 pr-4 text-right font-medium text-gray-500 uppercase">Qty</th>
                                            <th class="py-2 pr-4 text-right font-medium text-gray-500 uppercase">HPP (Rp)</th>
                                            <th class="py-2 pr-4 text-right font-medium text-gray-500 uppercase">Price (Rp)</th>
                                            <th class="py-2 text-right font-medium text-gray-500 uppercase">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tx->items as $di)
                                            <tr class="border-b border-gray-100 last:border-0">
                                                <td class="py-2 pr-4 text-gray-900">{{ $di->item?->name ?? '-' }}</td>
                                                <td class="py-2 pr-4 text-gray-500">{{ $di->item?->code ?? '-' }}</td>
                                                <td class="py-2 pr-4 text-gray-700">{{ $di->actual_size }}</td>
                                                <td class="py-2 pr-4 text-right text-gray-700 tabular-nums">{{ $di->quantity }}</td>
                                                <td class="py-2 pr-4 text-right text-gray-500 tabular-nums">{{ number_format($di->hpp, 0, ',', '.') }}</td>
                                                <td class="py-2 pr-4 text-right text-gray-700 tabular-nums font-medium">{{ number_format($di->selling_price_at_distribution, 0, ',', '.') }}</td>
                                                <td class="py-2 text-right text-gray-700 tabular-nums font-medium">{{ number_format($di->selling_price_at_distribution * $di->quantity, 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="py-3 text-center text-gray-400 italic">No items</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            @component('components.alpine-pagination', ['paginator' => $transactions])@endcomponent
        </div>
    @else
        <p class="text-sm text-gray-500">{{ __('No transactions yet.') }}</p>
    @endif
</div>
