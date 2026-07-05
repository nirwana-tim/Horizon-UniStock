<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Penerimaan Barang') }}</h2>
            <a href="{{ route('inventory.stock-receive.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('â† Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('No. Referensi') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $stockReceive->reference_number }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Vendor') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $stockReceive->vendor?->name ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Tanggal Terima') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $stockReceive->receive_date?->format('d/m/Y') }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $stockReceive->status === 'received' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($stockReceive->status) }}</span>
                                </dd>
                            </div>
                            @if($stockReceive->notes)
                            <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Catatan') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $stockReceive->notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <div class="mb-6 flex items-center gap-3">
                        <x-delete-modal
                            :route="route('inventory.stock-receive.destroy', $stockReceive)"
                            label="Hapus Penerimaan Barang"
                            description="Apakah Anda yakin ingin menghapus penerimaan {{ $stockReceive->reference_number }}? Data ini tidak dapat dikembalikan."
                        />
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Item Barang') }}</h3>
                        @if($stockReceive->items->count())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Item</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Varian</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">HPP</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($stockReceive->items as $item)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->item?->name ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->variant?->size ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->variant?->sku ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($item->hpp, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-semibold">Rp {{ number_format($item->quantity * $item->hpp, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="4" class="px-6 py-3 text-sm font-semibold text-gray-900">{{ __('Total') }}</td>
                                            <td class="px-6 py-3 text-right text-sm font-semibold text-gray-900">{{ number_format($stockReceive->items->sum('quantity'), 0, ',', '.') }}</td>
                                            <td colspan="2"></td>
                                            <td class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Rp {{ number_format($stockReceive->items->sum(fn ($i) => $i->quantity * $i->hpp), 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">{{ __('Tidak ada item.') }}</p>
                        @endif
                    </div>

                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Informasi Vendor') }}</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Nama Vendor') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $stockReceive->vendor?->name ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $stockReceive->vendor?->email ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Kontak') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $stockReceive->vendor?->phone ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
