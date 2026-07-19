@if($student->distributionTransactions->count())
    <div class="mt-8 pt-4 border-t border-gray-200">
        <h3 class="text-sm font-medium text-gray-500 mb-4">Distribution History</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Schedule</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($student->distributionTransactions as $tx)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $tx->schedule->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-sm">
                                @if($tx->status === 'completed')
                                    <x-badge type="success">Completed</x-badge>
                                @elseif($tx->status === 'partial')
                                    <x-badge type="warning">Partial</x-badge>
                                @else
                                    <x-badge type="danger">Cancelled</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $tx->items->pluck('item.name')->implode(', ') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $tx->pickup_time ? $tx->pickup_time->format('d/m/Y H:i:s') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif