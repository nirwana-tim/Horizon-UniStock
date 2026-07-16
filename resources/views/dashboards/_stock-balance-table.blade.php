                @forelse($stockBalances as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500 w-10">
                            {{ $loop->iteration + ($stockBalances->currentPage() - 1) * $stockBalances->perPage() }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap">
                            <a href="{{ route('master-data.item.show', $item) }}"
                               class="text-sm font-medium text-primary-700 hover:text-primary-800 hover:underline transition-colors">
                                {{ $item->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->category?->label ?? '-' }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-800 font-semibold text-right">
                            {{ number_format($item->stock_balances_sum_quantity ?? 0) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center text-gray-400">
                                <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>
                                <span class="text-sm">Belum ada data stok</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
