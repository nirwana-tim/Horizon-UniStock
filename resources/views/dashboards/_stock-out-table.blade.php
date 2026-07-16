                @forelse($stockOuts as $movement)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500 w-10">
                            {{ $loop->iteration + ($stockOuts->currentPage() - 1) * $stockOuts->perPage() }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm font-medium text-gray-800">
                            {{ $movement->item?->name ?? '-' }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-800 font-semibold text-right">
                            {{ number_format($movement->quantity) }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500">
                            @php
                                $refClass = $movement->reference_type ? class_basename($movement->reference_type) : null;
                                $keterangan = $movement->notes ?? match($refClass) {
                                    'DistributionTransaction' => 'Distribusi Mahasiswa',
                                    'StockOpnameAdjustment' => 'Penyesuaian Opname',
                                    default => $refClass ?? '-',
                                };
                            @endphp
                            {{ $keterangan }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-400 text-right">
                            {{ $movement->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center text-gray-400">
                                <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <span class="text-sm">Belum ada stok keluar</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
