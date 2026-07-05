<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))<div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md">{{ session('success') }}</div>@endif
                    @if(session('error'))<div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-md">{{ session('error') }}</div>@endif

                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Penerimaan Barang</h3>
                        <a href="{{ route('inventory.stock-receive.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:bg-primary-800 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('+ Terima Barang') }}</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Referensi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
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
                                             <a href="{{ route('inventory.stock-receive.show', $receive) }}" class="inline-flex items-center px-2.5 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Lihat</a>
                                            <x-delete-modal
                                                :route="route('inventory.stock-receive.destroy', $receive)"
                                                label="Hapus Penerimaan Barang"
                                                description="Apakah Anda yakin ingin menghapus penerimaan {{ $receive->reference_number }}? Data ini tidak dapat dikembalikan."
                                            />
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('Belum ada penerimaan barang.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $receives->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
