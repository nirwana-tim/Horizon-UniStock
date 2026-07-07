                @forelse($itemPrices as $index => $itemPrice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $itemPrices->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $itemPrice->item->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">{{ $itemPrice->item->code ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">Rp {{ number_format($itemPrice->selling_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">Rp {{ number_format($itemPrice->hpp, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $itemPrice->effective_date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
                              <a href="{{ route('master-data.item-price.show', $itemPrice) }}" class="inline-flex items-center px-2.5 py-1 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">View</a>
                            <x-delete-modal
                                :route="route('master-data.item-price.destroy', $itemPrice)"
                                label="Delete Item Price"
                                description="Are you sure you want to delete this price data? This action cannot be undone."
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No price data found.</td>
                    </tr>
                @endforelse
