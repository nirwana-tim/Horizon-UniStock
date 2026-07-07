                @forelse($vendors as $vendor)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $loop->iteration + ($vendors->currentPage() - 1) * $vendors->perPage() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('master-data.vendor.show', $vendor) }}" class="text-sm font-medium text-primary-600 hover:text-primary-900">
                                {{ $vendor->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vendor->email ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vendor->contact ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vendor->phone ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
                              <a href="{{ route('master-data.vendor.show', $vendor) }}" class="inline-flex items-center px-2.5 py-1 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">View</a>
                            <x-delete-modal
                                :route="route('master-data.vendor.destroy', $vendor)"
                                label="Delete Vendor"
                                description="Are you sure you want to delete vendor {{ $vendor->name }}? This action cannot be undone."
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            {{ __('No vendor data found.') }}
                        </td>
                    </tr>
                @endforelse
