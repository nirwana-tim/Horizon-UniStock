{{-- Items Already Received --}}
<div class="mt-6 pt-4 border-t border-gray-200">
    <h3 class="text-sm font-medium text-gray-500 mb-4">Received Items</h3>
    @if($receivedItems && $receivedItems->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Size</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Schedule</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($receivedItems as $ri)
                        @foreach($ri['details'] as $detail)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $ri['item']->name ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm font-mono text-gray-500">{{ $ri['item']->code ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm text-center text-gray-900">{{ $detail['quantity'] }}</td>
                                <td class="px-4 py-2 text-sm text-center text-gray-900">{{ $detail['size'] ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $detail['schedule'] }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $detail['date'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-sm text-gray-500 italic">No items received yet.</p>
    @endif
</div>