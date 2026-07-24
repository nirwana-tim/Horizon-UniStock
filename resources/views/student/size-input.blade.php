<x-app-layout>
    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    @if(session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    @php
        $filledCount = $entitlementItems->filter(fn($i) => !empty($existingSizes[$i->id] ?? ''))->count();
        $totalCount = $entitlementItems->count();
    @endphp

    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800">Input Ukuran Seragam</h2>
        <p class="text-xs text-gray-500 mt-0.5">Pilih ukuran untuk setiap item seragam kamu</p>
    </div>

    <div class="mb-3">
        <a href="{{ route('student.sizes.index') }}" class="inline-flex items-center text-xs text-primary-600 hover:text-primary-700 font-medium">
            <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke daftar event
        </a>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-700">
                <p class="font-semibold">{{ $event->title }}</p>
                <p class="mt-0.5">Deadline: {{ $event->end_date->format('d M Y H:i') }}</p>
                @if($event->description)
                    <p class="mt-0.5 text-blue-600">{{ $event->description }}</p>
                @endif
            </div>
        </div>
    </div>

        @if($totalCount > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-600">Progress pengisian</span>
                <span class="text-xs font-semibold text-primary-700">{{ $filledCount }}/{{ $totalCount }} ukuran</span>
            </div>
            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-primary-600 rounded-full transition-all duration-500"
                     style="width: {{ $totalCount > 0 ? ($filledCount / $totalCount) * 100 : 0 }}%"></div>
            </div>
        </div>
        @endif

        @if(!$canEdit)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm text-amber-700">Kamu sudah mencapai batas maksimal pengisian ({{ $event->max_changes }}x) untuk event ini.</p>
                </div>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-blue-700">Kesempatan mengisi: <strong>{{ $remainingChanges }}x</strong> lagi</p>
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
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <div class="space-y-3 mb-6">
                    @foreach($entitlementItems as $item)
                        @php
                            $currentSize = $existingSizes[$item->id] ?? '';
                        @endphp

                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-9 h-9 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-800 truncate">{{ $item->name }}</h3>
                                    <p class="text-xs text-gray-400 truncate">{{ $item->base_code }} &bull; {{ $item->unit }}</p>
                                </div>
                                @if(!empty($currentSize))
                                    <span class="text-xs font-medium text-primary-600 bg-primary-50 px-2.5 py-1 rounded-full flex-shrink-0">Terisi</span>
                                @endif
                            </div>

                            @if(!$canEdit)
                                @php
                                    $currentVariant = $item->variants->firstWhere('size', $currentSize);
                                    $sizeDisplay = $currentVariant ? $currentVariant->size_label : $currentSize;
                                @endphp
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3">
                                    <span class="text-sm text-gray-500">Ukuran terpilih:</span>
                                    <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-semibold">{{ $sizeDisplay }}</span>
                                </div>
                                <input type="hidden" name="sizes[{{ $item->id }}]" value="{{ $currentSize }}">
                            @elseif($item->variants->isNotEmpty())
                                <div class="flex flex-wrap gap-2" x-data="{ selected: '{{ $currentSize }}' }">
                                    @foreach($item->variants as $variant)
                                        <label class="relative cursor-pointer">
                                            <input type="radio"
                                                   name="sizes[{{ $item->id }}]"
                                                   value="{{ $variant->size }}"
                                                   class="sr-only peer"
                                                   {{ $currentSize == $variant->size ? 'checked' : '' }}
                                                   {{ !$currentSize && $loop->first ? '' : '' }}
                                                   required>
                                            <span class="inline-flex items-center justify-center min-w-[3rem] h-10 px-3 text-sm font-medium rounded-lg border-2 transition-all
                                                         peer-checked:border-primary-700 peer-checked:bg-primary-50 peer-checked:text-primary-700 peer-checked:font-semibold
                                                         {{ $currentSize == $variant->size ? 'border-primary-700 bg-primary-50 text-primary-700 font-semibold' : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                                                {{ $variant->size_label }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error("sizes.{$item->id}")
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            @else
                                <p class="text-sm text-gray-400 italic">Tidak tersedia varian ukuran</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($canEdit)
                <button type="submit"
                    class="w-full h-12 bg-primary-700 text-white text-sm font-semibold rounded-lg hover:bg-primary-800 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-300 transition-colors">
                    Simpan Ukuran
                </button>
                @endif
            </form>
        @endif
</x-app-layout>
