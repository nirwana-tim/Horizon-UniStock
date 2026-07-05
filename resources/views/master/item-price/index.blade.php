<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Harga Item</h3>
                        <a href="{{ route('master-data.item-price.create') }}" class="inline-flex items-center px-4 py-2 bg-[#980416] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#7a0311] transition">
                            {{ __('Tambah Harga') }}
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Jual</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">HPP</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Efektif Dari</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($itemPrices as $index => $itemPrice)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $itemPrices->firstItem() + $index }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $itemPrice->item->name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm font-mono text-gray-500">{{ $itemPrice->item->code ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-900">Rp {{ number_format($itemPrice->selling_price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-900">Rp {{ number_format($itemPrice->hpp, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $itemPrice->effective_date?->format('d/m/Y') ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-right space-x-1.5">
                                              <a href="{{ route('master-data.item-price.show', $itemPrice) }}" class="inline-flex items-center px-2.5 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Lihat</a>
                                            <x-delete-modal
                                                :route="route('master-data.item-price.destroy', $itemPrice)"
                                                label="Hapus Harga Item"
                                                description="Apakah Anda yakin ingin menghapus data harga ini? Data ini tidak dapat dikembalikan."
                                            />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">Belum ada data harga.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $itemPrices->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
