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
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

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
                            <p class="mt-1 text-sm text-gray-900">{{ $item->category->code ?? '' }} - {{ $item->category->label ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Gender</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ ['L' => 'Laki - Laki', 'P' => 'Perempuan', 'U' => 'Unisex'][$item->gender] ?? $item->gender ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Tipe</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->type?->code ?? '-' }} - {{ $item->type?->label ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Departemen</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->department?->code ?? '-' }} - {{ $item->department?->label ?? '-' }}</p>
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

                    {{-- Varian Section --}}
                    <div class="mt-8 pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-500 mb-4">Varian</h3>

                        {{-- Form Tambah Varian --}}
                        <form action="{{ route('master.item.variant.store', $item) }}" method="POST" class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            @csrf
                            <div class="flex items-end gap-3">
                                <div class="flex-1">
                                    <label for="size_id" class="block text-xs font-medium text-gray-500 mb-1">Ukuran</label>
                                    <select name="size_id" id="size_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 sm:text-sm">
                                        <option value="">-- Pilih Ukuran --</option>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}">{{ $size->code }} - {{ $size->label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label for="size" class="block text-xs font-medium text-gray-500 mb-1">Label Ukuran</label>
                                    <input type="text" name="size" id="size" required placeholder="S, M, L, XL, 40, 42" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 sm:text-sm">
                                </div>
                                <div class="flex-1">
                                    <label for="sku" class="block text-xs font-medium text-gray-500 mb-1">SKU (opsional)</label>
                                    <input type="text" name="sku" id="sku" placeholder="Auto jika kosong" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 sm:text-sm">
                                </div>
                                <div class="flex-1">
                                    <label for="weight" class="block text-xs font-medium text-gray-500 mb-1">Berat (opsional)</label>
                                    <input type="number" name="weight" id="weight" min="0" step="0.01" placeholder="0" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 sm:text-sm">
                                </div>
                                <button type="submit" class="px-4 py-2 bg-[#980416] text-white text-sm font-medium rounded-md hover:bg-[#7a0311] transition">
                                    Tambah
                                </button>
                            </div>
                            @error('size_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            @error('size') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            @error('sku') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </form>

                        {{-- Tabel Varian --}}
                        @if($item->variants->count())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ukuran</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Berat</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($item->variants as $variant)
                                            <tr>
                                                <td class="px-4 py-2 text-sm">{{ $variant->size_label ?? $variant->size }}</td>
                                                <td class="px-4 py-2 text-sm font-mono">{{ $variant->sku }}</td>
                                                <td class="px-4 py-2 text-sm">{{ $variant->weight ? $variant->weight . ' kg' : '-' }}</td>
                                                <td class="px-4 py-2 text-sm text-right">
                                                    <form action="{{ route('master.item.variant.destroy', [$item, $variant]) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus varian ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium">Hapus</button>
                                                    </form>
                                                </td>
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
