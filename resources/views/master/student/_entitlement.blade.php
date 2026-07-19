{{-- Entitlements (To Receive) --}}
<div class="mt-8 pt-4 border-t border-gray-200">
    <h3 class="text-sm font-medium text-gray-500 mb-4">Entitlements (To Receive)</h3>
    @if($entitlement && $entitlement->items->count())
        <div class="mb-2">
            <span class="text-xs text-gray-400">Entitlement Code:</span>
            <span class="ml-1 text-xs font-mono font-medium text-gray-700">{{ $entitlement->code }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Received</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $receivedMap = $receivedItems ?? collect(); @endphp
                    @foreach($entitlement->items as $ei)
                        @php
                            $baseCode = $ei->item->base_code ?? $ei->item->code;
                            $received = $receivedMap->get($baseCode, ['total_qty' => 0]);
                            $rQty = $received['total_qty'] ?? 0;
                            $eQty = $ei->quantity;
                            if ($rQty >= $eQty) {
                                $status = 'Complete';
                                $badgeType = 'success';
                            } elseif ($rQty > 0) {
                                $status = 'Partial';
                                $badgeType = 'warning';
                            } else {
                                $status = 'Pending';
                                $badgeType = 'neutral';
                            }
                        @endphp
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $ei->item->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-sm font-mono text-gray-500">{{ $baseCode }}</td>
                            <td class="px-4 py-2 text-sm text-center text-gray-900">{{ $eQty }}</td>
                            <td class="px-4 py-2 text-sm text-center text-gray-900">{{ $rQty }}</td>
                            <td class="px-4 py-2 text-sm text-center">
                                <x-badge :type="$badgeType">{{ $status }}</x-badge>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-4 bg-gray-50 border rounded-lg">
            <p class="text-sm text-gray-500 italic">No matching entitlement data found in the system.</p>
            <p class="text-xs text-gray-400 mt-2">Student Entitlement Code (Based on Batch & Study Program): <strong class="font-mono text-gray-700">{{ $student->entitlement_code ?? '(Not Calculated)' }}</strong></p>
        </div>
    @endif
</div>