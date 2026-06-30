<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Input Ukuran') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-700 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                        <p class="text-sm text-blue-700">
                            <strong>Periode Aktif:</strong> {{ $activePeriod->name }}<br>
                            <strong>Deadline Input Ukuran:</strong>
                            {{ $activePeriod->size_change_deadline ? $activePeriod->size_change_deadline->format('d M Y H:i') : 'Tidak ditentukan' }}
                        </p>
                        @if(!$canUpdate)
                            <p class="mt-2 text-sm text-amber-600">
                                <strong>Catatan:</strong> Anda sudah melakukan perubahan ukuran sebelumnya. Maksimal 1 kali perubahan diperbolehkan.
                            </p>
                        @endif
                    </div>

                    @if($entitlementItems->isEmpty())
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                            <p class="text-sm text-yellow-700">
                                Anda tidak memiliki hak barang untuk periode ini. Silakan hubungi bagian Finance untuk informasi lebih lanjut.
                            </p>
                        </div>
                    @else
                        <form action="{{ route('student.size.store') }}" method="POST">
                            @csrf

                            <div class="space-y-6">
                                @foreach($entitlementItems as $item)
                                    @php
                                        $isClothing = in_array($item->category->name ?? '', ['Seragam', 'Uniform', 'Clothing']);
                                        $isShoes = in_array($item->category->name ?? '', ['Sepatu', 'Shoes']);
                                        $currentSize = $existingSizes[$item->id] ?? '';
                                        $sizeItem = $student->activeSizeProfile
                                            ? $student->activeSizeProfile->sizeItems->where('item_id', $item->id)->first()
                                            : null;
                                        $hasChanged = $sizeItem && $sizeItem->change_count >= 1;
                                    @endphp

                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900">{{ $item->name }}</h3>
                                                <p class="text-sm text-gray-500">Kode: {{ $item->code }}</p>
                                                <p class="text-sm text-gray-500">Satuan: {{ $item->unit }}</p>
                                            </div>
                                            <div class="w-48">
                                                @if($hasChanged && !$canUpdate)
                                                    <div class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 sm:text-sm p-2">
                                                        {{ $currentSize }}
                                                    </div>
                                                    <input type="hidden" name="sizes[{{ $item->id }}]" value="{{ $currentSize }}">
                                                    <p class="mt-1 text-xs text-amber-500">Sudah diubah</p>
                                                @else
                                                    <label for="size_{{ $item->id }}" class="block text-sm font-medium text-gray-700">
                                                        Pilih Ukuran <span class="text-red-500">*</span>
                                                    </label>
                                                    <select name="sizes[{{ $item->id }}]" id="size_{{ $item->id }}" required
                                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option value="">-- Pilih Ukuran --</option>
                                                        @if($isShoes)
                                                            @foreach(range(38, 46) as $size)
                                                                <option value="{{ $size }}" {{ $currentSize == $size ? 'selected' : '' }}>
                                                                    {{ $size }}
                                                                </option>
                                                            @endforeach
                                                        @else
                                                            @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)
                                                                <option value="{{ $size }}" {{ $currentSize == $size ? 'selected' : '' }}>
                                                                    {{ $size }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                @endif
                                                @error("sizes.{$item->id}")
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-8 flex items-center gap-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Simpan Ukuran') }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
