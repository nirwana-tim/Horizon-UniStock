<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))<div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md">{{ session('success') }}</div>@endif

                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Entitlement</h3>
                        <a href="{{ route('distribution.entitlement.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:bg-primary-800 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('+ Tambah Entitlement') }}</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($entitlements as $entitlement)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration + ($entitlements->currentPage() - 1) * $entitlements->perPage() }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap"><a href="{{ route('distribution.entitlement.show', $entitlement) }}" class="text-sm font-medium text-primary-600 hover:text-primary-900 font-mono">{{ $entitlement->code }}</a></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $entitlement->description ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $entitlement->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $entitlement->is_active ? 'Active' : 'Inactive' }}</span></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $entitlement->items->count() }} item</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
                                              <a href="{{ route('distribution.entitlement.show', $entitlement) }}" class="inline-flex items-center px-2.5 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Lihat</a>
                                            <x-delete-modal
                                                :route="route('distribution.entitlement.destroy', $entitlement)"
                                                label="Hapus Entitlement"
                                                description="Apakah Anda yakin ingin menghapus entitlement {{ $entitlement->code }}? Data ini tidak dapat dikembalikan."
                                            />
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('Belum ada data entitlement.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $entitlements->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
