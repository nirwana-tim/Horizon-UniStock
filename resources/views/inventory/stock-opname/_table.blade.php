@forelse($batches as $index => $batch)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $batches->firstItem() + $index }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $batch->reference_number }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $batch->period }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $batch->opname_date->format('d/m/Y') }}</td>
        <td class="px-6 py-4 whitespace-nowrap">
            @if($batch->status === 'draft')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
            @elseif($batch->status === 'completed')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Completed</span>
            @elseif($batch->status === 'adjusted')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Adjusted</span>
            @else
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $batch->status }}</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $batch->creator->name ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
             <a href="{{ route('inventory.stock-opname.show', $batch) }}" class="inline-flex items-center px-2.5 py-1 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">View</a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No stock opname data found.</td>
    </tr>
@endforelse

