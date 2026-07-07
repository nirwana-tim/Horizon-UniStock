                @forelse($receives as $receive)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration + ($receives->currentPage() - 1) * $receives->perPage() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap"><a href="{{ route('inventory.stock-receive.show', $receive) }}" class="text-sm font-medium text-primary-600 hover:text-primary-900">{{ $receive->reference_number }}</a></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $receive->vendor?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $receive->receive_date?->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $receive->items->count() }} item</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $receive->status === 'received' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($receive->status) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
                             <a href="{{ route('inventory.stock-receive.show', $receive) }}" class="inline-flex items-center px-2.5 py-1 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">View</a>
                            <x-delete-modal
                                :route="route('inventory.stock-receive.destroy', $receive)"
                                label="Delete Stock Receive"
                                description="Are you sure you want to delete {{ $receive->reference_number }}? This action cannot be undone."
                            />
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No stock receive data found.') }}</td></tr>
                @endforelse
