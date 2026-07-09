@forelse($schedules as $schedule)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration + ($schedules->currentPage() - 1) * $schedules->perPage() }}</td>
        <td class="px-6 py-4 whitespace-nowrap"><a href="{{ route('distribution.distribution-schedule.show', $schedule) }}" class="text-sm font-medium text-primary-600 hover:text-primary-900">{{ $schedule->name }}</a></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $schedule->programLevel?->name ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $schedule->faculty?->name ?? '-' }}{{ $schedule->studyProgram ? ' / ' . $schedule->studyProgram->name : '' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $schedule->period ?? '-' }}{{ $schedule->semester ? ' - ' . $schedule->semester : '' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $schedule->date?->format('d/m/Y') ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $schedule->location }}</td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $schedule->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $schedule->is_active ? 'Active' : 'Inactive' }}</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
              <a href="{{ route('distribution.distribution-schedule.show', $schedule) }}" class="inline-flex items-center px-2.5 py-1 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">View</a>
            <x-delete-modal
                :route="route('distribution.distribution-schedule.destroy', $schedule)"
                label="Delete Distribution Schedule"
                description="Are you sure you want to delete schedule {{ $schedule->name }}? This data cannot be restored."
            />
        </td>
    </tr>
@empty
    <tr><td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No distribution schedule found.') }}</td></tr>
@endforelse

