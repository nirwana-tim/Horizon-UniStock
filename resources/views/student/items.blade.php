<x-app-layout>
    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800">Perlengkapan Seragam</h2>
        <p class="text-xs text-gray-500 mt-0.5">Status item seragam yang akan & sudah kamu terima</p>
    </div>

    @php
        $pendingItems = $entitlementItems->filter(fn($item) => !in_array($item->id, $receivedItemIds));
    @endphp

    {{-- Belum Diterima --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-sm font-semibold text-gray-700">Belum Diterima</h3>
            @if($entitlementItems->isNotEmpty())
                <span class="ml-auto text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">{{ $pendingItems->count() }} item</span>
            @endif
        </div>

        @if($pendingItems->isNotEmpty())
            <div class="space-y-2">
                @foreach($pendingItems as $item)
                    <div class="flex items-center gap-3 p-3 bg-amber-50/50 border border-amber-100 rounded-lg">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item->name }}</p>
                            @if(isset($selectedSizes[$item->id]) && !empty($selectedSizes[$item->id]))
                                <p class="text-xs text-gray-500 mt-0.5">Ukuran: <strong>{{ is_array($selectedSizes[$item->id]) ? ($selectedSizes[$item->id]['size'] ?? '-') : $selectedSizes[$item->id] }}</strong></p>
                            @else
                                <p class="text-xs text-amber-600 mt-0.5">Ukuran belum dipilih</p>
                            @endif
                        </div>
                        @if(isset($selectedSizes[$item->id]) && !empty($selectedSizes[$item->id]))
                            <span class="text-xs font-medium text-gray-600 bg-white border border-gray-200 px-2.5 py-1 rounded-full flex-shrink-0">
                                {{ is_array($selectedSizes[$item->id]) ? $selectedSizes[$item->id]['size'] : $selectedSizes[$item->id] }}
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif($entitlementItems->isNotEmpty())
            <div class="flex items-center gap-2 py-3">
                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-green-600 font-medium">Semua item sudah diterima! ✅</p>
            </div>
        @else
            <div class="flex flex-col items-center gap-2 py-6 text-center">
                <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-sm text-gray-400">Tidak ada data entitlement</p>
            </div>
        @endif
    </div>

    {{-- Sudah Diterima --}}
    @php $receivedCount = $receivedTransactions->sum(fn($tx) => $tx->items->count()); @endphp
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-20">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-sm font-semibold text-gray-700">Sudah Diterima</h3>
            @if($receivedCount > 0)
                <span class="ml-auto text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">{{ $receivedCount }} item</span>
            @endif
        </div>

        @if($receivedTransactions->isNotEmpty())
            <div class="space-y-2">
                @foreach($receivedTransactions as $tx)
                    @foreach($tx->items as $item)
                    <div x-data="{ open: false }" class="border border-gray-100 rounded-lg overflow-hidden transition-shadow hover:shadow-sm">
                        <button @click="open = !open"
                                class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50 transition-colors">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-800 flex-1 min-w-0 truncate">{{ $item->item?->name ?? 'Item' }}</span>
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full flex-shrink-0">{{ $item->quantity }} pcs</span>
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200"
                                 :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse>
                            <div class="px-4 pb-3 pt-3 border-t border-gray-100 bg-gray-50/50">
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="flex items-center gap-1.5 text-gray-500">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $tx->pickup_time?->format('d/m/Y H:i') ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-gray-500">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="truncate">{{ $tx->schedule?->location ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-gray-500">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Ukuran: {{ $item->actual_size }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                            {{ $tx->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $tx->status === 'completed' ? 'Lengkap' : 'Sebagian' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center gap-2 py-6 text-center">
                <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-sm text-gray-400">Belum ada item yang diterima</p>
            </div>
        @endif
    </div>
</x-app-layout>