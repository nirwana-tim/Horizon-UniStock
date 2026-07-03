<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Item') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('master.item.edit', $item) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('master.item.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Kembali') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Kode Item</h3>
                            <p class="mt-1 text-sm font-mono text-gray-900">{{ $item->code }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Nama Item</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Kategori</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->category->code ?? '' }} - {{ $item->category->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Gender</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ ['L' => 'Laki - Laki', 'P' => 'Perempuan', 'U' => 'Unisex'][$item->gender] ?? $item->gender ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Tipe</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->type?->code ?? '-' }} - {{ $item->type?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Departemen</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->department?->code ?? '-' }} - {{ $item->department?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Satuan</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->unit }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Harga Jual</h3>
                            <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($item->selling_price, 2) }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">HPP</h3>
                            <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($item->hpp, 2) }}</p>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-500 mb-4">Varian</h3>
                        @if($item->variants->count())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ukuran</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($item->variants as $variant)
                                            <tr>
                                                <td class="px-4 py-2 text-sm">{{ $variant->size_label ?? $variant->size }}</td>
                                                <td class="px-4 py-2 text-sm font-mono">{{ $variant->sku }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Tidak ada varian.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
