@forelse($levels as $level)
    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $level->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $level->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $level->students_count ?? 0 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
                              <a href="{{ route('master-data.program-level.show', $level) }}" class="inline-flex items-center px-2.5 py-1 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">View</a>
                            <x-delete-modal
                                :route="route('master-data.program-level.destroy', $level)"
                                label="Delete Program Level"
                                description="Are you sure you want to delete this program level? This action cannot be undone."
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No data found.</td>
                    </tr>
                @endforelse
