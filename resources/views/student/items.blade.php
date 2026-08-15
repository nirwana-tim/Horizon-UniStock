<x-app-layout>
    @php
        $pendingItems = $entitlementItems->filter(fn($item) => !in_array($item->id, $receivedItemIds));
        $receivedCount = $receivedTransactions->sum(fn($tx) => $tx->items->count());
    @endphp

    <main class="min-h-screen pb-8">
        {{-- Tabs --}}
        <nav class="flex w-full bg-white/90 backdrop-blur-sm sticky top-16 z-40 shadow-[0_1px_0_rgba(0,0,0,0.04)]">
            <button type="button"
                    class="flex-1 relative py-4 text-center font-label-md text-label-md transition-colors duration-150 active:scale-95 {{ request()->input('tab', 'pending') === 'pending' ? 'text-primary' : 'text-black/30' }}"
                    onclick="document.getElementById('tab-pending').style.display='';document.getElementById('tab-received').style.display='none';this.classList.add('text-primary');this.classList.remove('text-black/30');this.nextElementSibling.classList.add('text-black/30');this.nextElementSibling.classList.remove('text-primary');">
                Pending
                @if(request()->input('tab', 'pending') === 'pending')
                    <div class="active-tab-indicator"></div>
                @endif
            </button>
            <button type="button"
                    class="flex-1 relative py-4 text-center font-label-md text-label-md transition-colors duration-150 active:scale-95 {{ request()->input('tab') === 'received' ? 'text-primary' : 'text-black/30' }}"
                    onclick="document.getElementById('tab-received').style.display='';document.getElementById('tab-pending').style.display='none';this.classList.add('text-primary');this.classList.remove('text-black/30');this.previousElementSibling.classList.add('text-black/30');this.previousElementSibling.classList.remove('text-primary');">
                Received
                @if(request()->input('tab') === 'received')
                    <div class="active-tab-indicator"></div>
                @endif
            </button>
        </nav>

        {{-- Counter --}}
        <div class="px-container-margin pt-4 flex justify-between items-center">
            <span class="text-secondary/50 font-label-md text-label-md" id="items-counter">{{ $pendingItems->count() }} ITEMS PENDING</span>
        </div>

        {{-- Pending List --}}
        <div id="tab-pending" class="px-container-margin mt-4 space-y-4" style="{{ request()->input('tab') !== 'received' ? '' : 'display:none' }}">
            @if($pendingItems->isNotEmpty())
                @foreach($pendingItems as $item)
                    <div class="bg-white rounded-2xl p-5 shadow-card flex items-center justify-between active:scale-[0.99] transition-transform duration-200 cursor-pointer"
                         data-modal="barang:{{ $item->id }}"
                         data-item-id="{{ $item->id }}"
                         data-item-name="{{ $item->name }}"
                         data-item-size="{{ is_array($selectedSizes[$item->id] ?? null) ? ($selectedSizes[$item->id]['size'] ?? '') : ($selectedSizes[$item->id] ?? '') }}"
                         data-item-status="pending">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-primary/5 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">checkroom</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <h3 class="font-headline-sm text-[15px] text-on-surface">{{ $item->name }}</h3>
                            @if(isset($selectedSizes[$item->id]) && !empty($selectedSizes[$item->id]))
                                <div class="inline-flex items-center px-2.5 py-1 rounded-full bg-surface-variant/40 w-fit">
                                    <span class="text-[10px] font-semibold text-secondary uppercase tracking-wider">Ukuran {{ is_array($selectedSizes[$item->id]) ? ($selectedSizes[$item->id]['size'] ?? '-') : $selectedSizes[$item->id] }}</span>
                                </div>
                            @else
                                <span class="text-[10px] font-semibold text-amber-600 uppercase tracking-wider">Ukuran belum dipilih</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="font-semibold font-label-md text-label-md text-primary">Pending</span>
                        <span class="text-[10px] text-secondary/50">ID: #{{ $item->id }}</span>
                    </div>
                </div>
                @endforeach
            @else
                <div class="bg-white rounded-2xl p-8 text-center">
                    <span class="material-symbols-outlined text-4xl text-secondary/30 mb-3">inventory_2</span>
                    <p class="font-body-md text-body-md text-secondary/50">Tidak ada barang pending</p>
                </div>
            @endif
        </div>

        {{-- Received List --}}
        <div id="tab-received" class="px-container-margin mt-4 space-y-4" style="{{ request()->input('tab') === 'received' ? '' : 'display:none' }}">
            @if($receivedTransactions->isNotEmpty())
                @foreach($receivedTransactions as $tx)
                    @foreach($tx->items as $item)
                <div class="bg-white rounded-2xl p-5 shadow-card flex items-center justify-between active:scale-[0.99] transition-transform duration-200 cursor-pointer"
                     data-modal="barang:{{ $item->id }}"
                     data-item-id="{{ $item->id }}"
                     data-item-name="{{ $item->item?->name ?? 'Item' }}"
                     data-item-size="{{ is_array($selectedSizes[$item->id] ?? null) ? ($selectedSizes[$item->id]['size'] ?? '') : ($selectedSizes[$item->id] ?? '') }}"
                     data-item-status="received">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <span class="material-symbols-outlined text-emerald-600 text-2xl" style="font-variation-settings: 'FILL' 1;">checkroom</span>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <h3 class="font-headline-sm text-[15px] text-on-surface">{{ $item->item?->name ?? 'Item' }}</h3>
                                @if($item->actual_size)
                                    <div class="inline-flex items-center px-2.5 py-1 rounded-full bg-surface-variant/40 w-fit">
                                        <span class="text-[10px] font-semibold text-secondary uppercase tracking-wider">Ukuran {{ $item->actual_size }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="font-semibold font-label-md text-label-md text-emerald-600">Received</span>
                            <span class="text-[10px] text-secondary/50">{{ $item->quantity }} pcs</span>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            @else
                <div class="bg-white rounded-2xl p-8 text-center">
                    <span class="material-symbols-outlined text-4xl text-secondary/30 mb-3">inventory_2</span>
                    <p class="font-body-md text-body-md text-secondary/50">Tidak ada barang yang sudah diterima</p>
                </div>
            @endif
        </div>
    </main>
</x-app-layout>
