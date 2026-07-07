<div class="border-t border-gray-200 pt-6 mt-6">
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
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transactions as $tx)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tx->student?->name ?? '-' }} ({{ $tx->student?->nim ?? '-' }})</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tx->status === 'completed' ? 'bg-green-100 text-green-800' : ($tx->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ $tx->status }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tx->pickup_time?->format('d/m/Y H:i') ?? '-' }}</td>
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