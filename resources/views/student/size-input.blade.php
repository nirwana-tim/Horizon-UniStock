<x-app-layout>
    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    @if(session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800">Input Ukuran Seragam</h2>
        <p class="text-xs text-gray-500 mt-0.5">Pilih ukuran untuk setiap item seragam kamu</p>
    </div>

    @if(!$canUpdate)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-sm text-amber-700">Kamu sudah pernah mengubah ukuran. Maksimal 1 kali perubahan.</p>
            </div>
        </div>
    @endif

    @if($entitlementItems->isEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-center">
            <p class="text-sm text-amber-700">Kamu tidak memiliki entitlement item untuk periode ini. Silakan hubungi Finance.</p>
        </div>
    @else
        <form action="{{ route('student.sizes.store') }}" method="POST">
            @csrf

            <div class="space-y-3 mb-6">
                @foreach($entitlementItems as $item)
                    @php
                        $currentSize = $existingSizes[$item->id] ?? '';
                        $sizeItem = $student->activeSizeProfile
                            ? $student->activeSizeProfile->sizeItems->where('item_id', $item->id)->first()
                            : null;
                        $hasChanged = $sizeItem && $sizeItem->change_count >= 1;
                    @endphp

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                        <div class="mb-3">
                            <h3 class="text-sm font-semibold text-gray-800">{{ $item->name }}</h3>
                            <p class="text-xs text-gray-400">{{ $item->base_code }} &bull; {{ $item->unit }}</p>
                        </div>

                        @if($hasChanged && !$canUpdate)
                            @php
                                $currentVariant = $item->variants->firstWhere('size', $currentSize);
                                $sizeDisplay = $currentVariant ? $currentVariant->size_label : $currentSize;
                            @endphp
                            <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3">
                                <span class="text-sm text-gray-600">Ukuran terpilih:</span>
                                <span class="text-sm font-semibold text-gray-800">{{ $sizeDisplay }}</span>
                            </div>
                            <input type="hidden" name="sizes[{{ $item->id }}]" value="{{ $currentSize }}">
                        @else
                            <select name="sizes[{{ $item->id }}]" id="size_{{ $item->id }}" required
                                class="w-full h-11 px-3 text-sm bg-gray-100 border border-gray-200 rounded-lg text-gray-800 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-colors @error('sizes.{{ $item->id }}') border-red-400 @enderror">
                                <option value="">-- Pilih Ukuran --</option>
                                @forelse($item->variants as $variant)
                                    <option value="{{ $variant->size }}" {{ $currentSize == $variant->size ? 'selected' : '' }}>
                                        {{ $variant->size_label }}
                                    </option>
                                @empty
                                    <option value="">Tidak ada varian</option>
                                @endforelse
                            </select>
                            @error("sizes.{$item->id}")
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                @endforeach
            </div>

            <button type="submit"
                class="w-full h-12 bg-primary-700 text-white text-sm font-semibold rounded-lg hover:bg-primary-800 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-300 transition-colors">
                Simpan Ukuran
            </button>
        </form>
    @endif
</x-app-layout>
