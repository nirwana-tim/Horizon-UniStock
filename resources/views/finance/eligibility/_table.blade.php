@forelse($students as $student)
    @php
        $record = $student->eligibilityRecords->first();
        $isEligible = $record && $record->is_eligible;
        $paymentStatus = $record ? $record->payment_status : 'Unpaid';
    @endphp
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $student->nim }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ $student->name }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $student->studyProgram?->name ?? '-' }} ({{ $student->studyProgram?->faculty?->code ?? '-' }})
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm">
            @if($isEligible)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                    Eligible
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                    Not Eligible
                </span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ strtolower($paymentStatus) === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ $paymentStatus }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
            <form action="{{ route('finance.eligibility.toggle', $student) }}" method="POST" class="inline-block">
                @csrf
                @if($isEligible)
                    <button type="submit" class="inline-flex items-center px-2.5 py-1 border border-red-300 text-red-700 hover:bg-red-50 rounded-md text-xs font-semibold uppercase tracking-widest transition">
                        Set Unpaid
                    </button>
                @else
                    <button type="submit" class="inline-flex items-center px-2.5 py-1 bg-green-600 hover:bg-green-700 text-white rounded-md text-xs font-semibold uppercase tracking-widest transition">
                        Set Paid (Eligible)
                    </button>
                @endif
            </form>
        </td>
    </tr>
@empty
    <tr class="hover:bg-gray-50">
        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No student data found.</td>
    </tr>
@endforelse

