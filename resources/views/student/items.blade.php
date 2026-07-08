<x-app-layout>
    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800">Perlengkapan Seragam</h2>
        <p class="text-xs text-gray-500 mt-0.5">Status item seragam yang akan & sudah kamu terima</p>
    </div>

    {{-- Belum Diterima --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">
            Belum Diterima
            @if($entitlementItems->isNotEmpty())
                <span class="ml-1 text-xs text-gray-400">({{ $entitlementItems->count() }})</span>
            @endif
        </h3>

        @php
            $pendingItems = $entitlementItems->filter(fn($item) => !in_array($item->id, $receivedItemIds));
        @endphp

        @if($pendingItems->isNotEmpty())
            <div class="space-y-2">
                @foreach($pendingItems as $item)
                    <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                        <div class="w-2 h-2 rounded-full bg-amber-400 flex-shrink-0"></div>
                        <span class="text-sm text-gray-800">{{ $item->name }}</span>
                        @if(isset($selectedSizes[$item->id]))
                            <span class="text-xs text-gray-400 ml-auto">Ukuran: {{ $selectedSizes[$item->id] }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex items-center gap-2 py-2">
                <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-gray-500">Semua item sudah diterima</p>
            </div>
        @endif
    </div>

    {{-- Sudah Diterima --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-20">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">
            Sudah Diterima
            <span class="ml-1 text-xs text-gray-400">({{ $receivedTransactions->sum(fn($tx) => $tx->items->count()) }})</span>
        </h3>

        @if($receivedTransactions->isNotEmpty())
            <div class="space-y-2">
                @foreach($receivedTransactions as $tx)
                    @foreach($tx->items as $item)
                    <div x-data="{ open: false }" class="border border-gray-100 rounded-lg overflow-hidden">
                        <button @click="open = !open"
                                class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-800 flex-1 min-w-0 truncate">{{ $item->item?->name ?? 'Item' }}</span>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $item->quantity }} pcs</span>
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200"
                                 :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse>
                            <div class="px-4 pb-3 pt-2 border-t border-gray-50 text-xs text-gray-500 space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Diambil: {{ $tx->pickup_time?->format('d/m/Y H:i') ?? '-' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Lokasi: {{ $tx->schedule?->location ?? '-' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>Ukuran: {{ $item->actual_size }}</span>
                                </div>
                                @if($tx->schedule)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <span>Jadwal: {{ $tx->schedule->name }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $tx->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $tx->status === 'completed' ? 'Lengkap' : 'Sebagian' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>
        @else
            <div class="flex items-center gap-2 py-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-sm text-gray-400">Belum ada item yang diterima</p>
            </div>
        @endif
    </div>
</x-app-layout>