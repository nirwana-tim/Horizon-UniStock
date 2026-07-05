<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Entitlement') }}</h2>
            <a href="{{ route('distribution.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('← Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Kode Entitlement') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold font-mono">{{ $entitlement->code }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                                <dd class="mt-1"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $entitlement->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $entitlement->is_active ? 'Active' : 'Inactive' }}</span></dd>
                            </div>
                            @if($entitlement->description)
                            <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Deskripsi') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $entitlement->description }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <div class="mb-6 flex items-center gap-3">
                        <a href="{{ route('distribution.entitlement.edit', $entitlement) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Edit') }}</a>
                        <x-delete-modal
                            :route="route('distribution.entitlement.destroy', $entitlement)"
                            label="Hapus Entitlement"
                            description="Apakah Anda yakin ingin menghapus entitlement {{ $entitlement->code }}? Data ini tidak dapat dikembalikan."
                        />
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Daftar Item') }}</h3>
                        @if($entitlement->items->count())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Item</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Produk</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran Tersedia</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($entitlement->items as $ei)
                                            @php
                                                $item = $ei->item;
                                                $baseCode = $item->base_code ?? $item->code;
                                                $availableSizes = App\Models\Item::where('base_code', $baseCode)
                                                    ->with('variants')
                                                    ->get()
                                                    ->pluck('variants.0.size_label')
                                                    ->filter()
                                                    ->implode(', ');
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item?->name ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">{{ $baseCode }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $availableSizes ?: '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ $ei->quantity }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">{{ __('Belum ada item.') }}</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
