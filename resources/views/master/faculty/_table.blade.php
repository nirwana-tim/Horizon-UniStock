                @forelse($faculties as $faculty)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $loop->iteration + ($faculties->currentPage() - 1) * $faculties->perPage() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('master-data.faculty.show', $faculty) }}" class="text-sm font-medium text-primary-600 hover:text-primary-900">
                                {{ $faculty->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $faculty->code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $faculty->study_programs_count ?? $faculty->study_programs->count() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
                              <a href="{{ route('master-data.faculty.show', $faculty) }}" class="inline-flex items-center px-2.5 py-1 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">View</a>
                            <x-delete-modal
                                :route="route('master-data.faculty.destroy', $faculty)"
                                label="Delete Faculty"
                                description="Are you sure you want to delete faculty {{ $faculty->name }}? This action cannot be undone."
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            {{ __('No faculty data found.') }}
                        </td>
                    </tr>
                @endforelse
