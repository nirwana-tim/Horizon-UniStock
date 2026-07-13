<x-app-layout>

    <h2 class="text-lg font-bold text-gray-800 mb-4">Uniform Distribution</h2>

    {{-- Today's Activity Bar --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-4 flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-500">Distribusi Hari Ini</p>
            <p class="text-lg font-bold text-gray-800">{{ $todayCount }} transaksi</p>
        </div>
        @if($activeSchedule)
            <div class="text-right">
                <p class="text-xs text-gray-500">Jadwal Aktif</p>
                <p class="text-sm font-semibold text-primary-700">{{ $activeSchedule->name }}</p>
                <p class="text-[10px] text-gray-400">{{ $activeSchedule->date->format('d M Y') }}</p>
            </div>
        @else
            <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">Tidak ada jadwal aktif</span>
        @endif
    </div>

    {{-- Main Action Cards --}}
    <div class="grid grid-cols-1 gap-4 mb-6">

        {{-- Scan QR --}}
        <a href="{{ route('distribution.scan.index') }}"
           class="flex items-center gap-4 p-5 bg-primary-700 rounded-2xl shadow-md shadow-primary-200 hover:bg-primary-800 transition-colors group">
            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8H3a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2v-8a2 2 0 00-2-2h-2"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-base font-bold text-white">Scan Student QR</h3>
                <p class="text-sm text-primary-200 mt-0.5">Use camera to scan permanent QR</p>
            </div>
            <svg class="w-5 h-5 text-primary-300 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

        {{-- Cari Manual NIM --}}
        <div x-data="{ open: false }">
            <button @click="open = !open"
                    class="w-full flex items-center gap-4 p-5 bg-white border-2 border-gray-200 rounded-2xl hover:border-primary-300 hover:bg-primary-50 transition-all group">
                <div class="w-14 h-14 bg-gray-100 group-hover:bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                    <svg class="w-7 h-7 text-gray-500 group-hover:text-primary-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div class="flex-1 text-left">
                    <h3 class="text-base font-bold text-gray-800">Manual NIM Search</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Fallback if QR is unreadable</p>
                </div>
                <svg :class="open ? 'rotate-180' : ''"
                     class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="mt-2 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <form action="{{ route('distribution.search') }}" method="POST">
                    @csrf
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Student NIM</label>
                    <div class="flex gap-2">
                        <input type="text"
                               name="query"
                               placeholder="Type student NIM..."
                               required
                               class="flex-1 px-3 py-2.5 h-11 text-sm bg-white border border-gray-200 rounded-lg
                                      text-gray-800 placeholder-gray-400
                                      focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:bg-white
                                      transition-colors">
                        <button type="submit"
                                class="px-5 h-11 bg-primary-700 text-white text-sm font-medium rounded-lg
                                       hover:bg-primary-800 active:bg-primary-900 transition-colors flex-shrink-0">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- Recent Transactions --}}
    @if($recentTransactions->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Transaksi Terbaru</h3>
        <div class="space-y-2">
            @foreach($recentTransactions as $tx)
            <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-gray-500">{{ strtoupper(substr($tx->student?->nim ?? '?', -2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $tx->student?->user?->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-400">{{ $tx->items_count ?? 0 }} item · {{ $tx->status }}</p>
                </div>
                <span class="text-[10px] text-gray-400 flex-shrink-0">{{ $tx->created_at->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick Guide --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mt-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Distribution Guide</h3>
        <div class="space-y-3">
            @foreach([
                ['step' => '1', 'text' => 'Scan student QR or search manually by NIM'],
                ['step' => '2', 'text' => 'System displays student data & active entitlement stage'],
                ['step' => '3', 'text' => 'Check items given, edit size if needed'],
                ['step' => '4', 'text' => 'Validate stock — partial pickup if insufficient'],
                ['step' => '5', 'text' => 'Submit transaction to save & reduce stock'],
            ] as $guide)
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-xs font-bold text-primary-700">{{ $guide['step'] }}</span>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $guide['text'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

</x-app-layout>