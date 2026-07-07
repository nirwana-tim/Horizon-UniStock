<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Student Details</h2>
            <div class="flex gap-2">
                <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 transition">
                    Edit
                </a>
<a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                                    Back
                                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">NIM</h3>
                            <p class="mt-1 text-sm font-mono text-gray-900">{{ $student->nim }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Name</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Campus Email</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->email_kampus }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Personal Email</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->email_pribadi ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Study Program</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->studyProgram->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Level / Batch</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->programLevel->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Type</h3>
                            @if($student->student_type === 'freshman')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Freshman</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Continuing</span>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Account Status</h3>
                            @if($student->user_id)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                <span class="text-xs text-gray-500 ml-2">{{ $student->user?->email }}</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Not Generated</span>
                            @endif
                        </div>
                    </div>

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
                                                // Match received items by base_code (product group)
                                                $baseCode = $ei->item->base_code ?? $ei->item->code;
                                                $received = $receivedMap->get($baseCode, ['total_qty' => 0]);
                                                $rQty = $received['total_qty'] ?? 0;
                                                $eQty = $ei->quantity;
                                                if ($rQty >= $eQty) {
                                                    $status = 'Complete';
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                } elseif ($rQty > 0) {
                                                    $status = 'Partial';
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                } else {
                                                    $status = 'Pending';
                                                    $statusClass = 'bg-gray-100 text-gray-600';
                                                }
                                            @endphp
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900">{{ $ei->item->name ?? '-' }}</td>
                                                <td class="px-4 py-2 text-sm font-mono text-gray-500">{{ $baseCode }}</td>
                                                <td class="px-4 py-2 text-sm text-center text-gray-900">{{ $eQty }}</td>
                                                <td class="px-4 py-2 text-sm text-center text-gray-900">{{ $rQty }}</td>
                                                <td class="px-4 py-2 text-sm text-center">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">{{ $status }}</span>
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
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                                    @elseif($tx->status === 'partial')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Partial</span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Cancelled</span>
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
