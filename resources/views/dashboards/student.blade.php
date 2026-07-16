<x-app-layout>

    @php
        $hour = now()->format('H');
        if ($hour < 12) $greeting = 'Selamat Pagi';
        elseif ($hour < 15) $greeting = 'Selamat Siang';
        elseif ($hour < 18) $greeting = 'Selamat Sore';
        else $greeting = 'Selamat Malam';

        $steps = [
            ['key' => 'size', 'label' => 'Input Ukuran', 'done' => $hasFilledSize, 'href' => route('student.sizes.index'), 'action' => 'Isi Ukuran'],
            ['key' => 'qr', 'label' => 'QR Identitas', 'done' => $hasQr, 'href' => route('student.qr'), 'action' => 'Lihat QR'],
            ['key' => 'pickup', 'label' => 'Ambil Seragam', 'done' => $recentTransactions->where('status', 'completed')->count() > 0, 'href' => null, 'action' => null],
        ];
        $doneCount = collect($steps)->where('done', true)->count();
        $totalCount = count($steps);
    @endphp

    {{-- Hero --}}
    <div class="bg-gradient-to-br from-primary-700 to-primary-900 rounded-2xl p-6 mb-5 relative overflow-hidden">
        <div class="absolute top-[-30px] right-[-30px] w-28 h-28 bg-white/10 rounded-full"></div>
        <div class="absolute bottom-[-20px] left-[-20px] w-20 h-20 bg-primary-600/40 rounded-full"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 bg-white/20 border-2 border-white/30 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xl font-bold uppercase">
                    {{ substr($student->name, 0, 2) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-primary-200 text-xs">{{ $greeting }},</p>
                    <h2 class="text-white font-bold text-base leading-tight truncate">{{ $student->name }}</h2>
                    <p class="text-primary-200 text-xs mt-0.5 truncate">{{ $student->nim }} &bull; {{ $student->studyProgram?->name ?? '-' }}</p>
                </div>
            </div>

            {{-- Progress ringkasan --}}
            <div class="bg-white/10 rounded-xl p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-white/80 text-xs">Progress Pengambilan Seragam</span>
                    <span class="text-white font-semibold text-xs">{{ $doneCount }}/{{ $totalCount }}</span>
                </div>
                <div class="w-full h-2 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-white rounded-full transition-all duration-500"
                         style="width: {{ $totalCount > 0 ? ($doneCount / $totalCount) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Steps sebagai cards --}}
    <div class="space-y-3 mb-5">
        @foreach($steps as $i => $step)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden
                    {{ $step['done'] ? '' : 'border-primary-200' }}">
            <div class="flex items-center gap-3 p-4">
                {{-- Icon step --}}
                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                            {{ $step['done'] ? 'bg-green-100' : ($i === 0 || $steps[$i-1]['done'] ? 'bg-primary-100' : 'bg-gray-100') }}">
                    @if($step['done'])
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <span class="text-sm font-bold {{ $i === 0 || $steps[$i-1]['done'] ? 'text-primary-600' : 'text-gray-400' }}">{{ $i + 1 }}</span>
                    @endif
                </div>

                {{-- Label & Detail --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold {{ $step['done'] ? 'text-gray-500' : 'text-gray-800' }}">
                        {{ $step['label'] }}
                        @if($step['done'])
                            <span class="ml-1.5 text-green-600 font-normal">✓ Selesai</span>
                        @endif
                    </p>
                    @if($step['done'] && $step['key'] === 'size' && !empty($selectedSizes))
                        <div class="mt-1 flex flex-wrap gap-1">
                            @foreach(array_slice($selectedSizes, 0, 3) as $size)
                                <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                    {{ $size['name'] }}: <strong class="ml-0.5 text-gray-800">{{ $size['size'] }}</strong>
                                </span>
                            @endforeach
                            @if(count($selectedSizes) > 3)
                                <span class="text-xs text-gray-400">+{{ count($selectedSizes) - 3 }} lainnya</span>
                            @endif
                        </div>
                    @elseif($step['done'] && $step['key'] === 'qr')
                        <p class="text-xs text-gray-400 mt-0.5">QR Code siap digunakan</p>
                    @elseif($step['done'] && $step['key'] === 'pickup')
                        <p class="text-xs text-gray-400 mt-0.5">Seragam sudah diambil</p>
                    @elseif(!$step['done'] && $step['key'] === 'pickup' && $recentTransactions->where('status', 'partial')->count() > 0)
                        <p class="text-xs text-amber-500 mt-0.5">Masih ada item yang belum diambil</p>
                    @endif
                </div>

                {{-- Action --}}
                @if(!$step['done'] && $step['href'] && ($i === 0 || $steps[$i-1]['done']))
                    <a href="{{ $step['href'] }}"
                       class="text-xs font-medium text-white bg-primary-700 px-3.5 py-1.5 rounded-lg hover:bg-primary-800 transition-colors flex-shrink-0">
                        {{ $step['action'] }}
                    </a>
                @elseif($step['done'] && $step['href'])
                    <a href="{{ $step['href'] }}"
                       class="text-xs font-medium text-primary-700 bg-primary-50 px-3 py-1.5 rounded-lg hover:bg-primary-100 transition-colors flex-shrink-0">
                        Detail
                    </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Status Cards --}}
    <div class="grid grid-cols-2 gap-3 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs text-gray-500">Email Kampus</span>
            </div>
            @if($student->email_kampus)
                @if($student->email_verified_at)
                    <x-badge type="success">Terverifikasi</x-badge>
                @else
                    <x-badge type="warning">Belum Verifikasi</x-badge>
                @endif
            @else
                <x-badge type="neutral">Belum Diisi</x-badge>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
                <span class="text-xs text-gray-500">Status Pengambilan</span>
            </div>
            @php $latestStatus = $recentTransactions->first()?->status ?? null; @endphp
            @if($latestStatus === 'completed')
                <x-badge type="success">Selesai</x-badge>
            @elseif($latestStatus === 'partial')
                <x-badge type="warning">Sebagian</x-badge>
            @else
                <x-badge type="neutral">Belum Diambil</x-badge>
            @endif
        </div>
    </div>

    {{-- Jadwal Aktif --}}
    @if($activeSchedules->count())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Active Distribution Schedule</h3>
        <div class="space-y-3">
            @foreach($activeSchedules as $schedule)
            <div class="flex items-start gap-3 p-3 bg-primary-50 border border-primary-100 rounded-lg">
                <div class="w-9 h-9 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $schedule->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $schedule->location }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs font-medium text-primary-700">{{ \Carbon\Carbon::parse($schedule->date)->format('d M Y') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Riwayat Transaksi --}}
    @if($recentTransactions->count())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <div class="px-5 py-3.5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Pickup History</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($recentTransactions as $tx)
            <div class="flex items-center justify-between px-5 py-3">
                <div>
                    <p class="text-sm text-gray-800">{{ $tx->schedule?->name ?? 'Distribusi' }}</p>
                    <p class="text-xs text-gray-400">{{ $tx->pickup_time?->format('d/m/Y H:i') ?? '-' }}</p>
                </div>
                @if($tx->status === 'completed')
                    <x-badge type="success">Completed</x-badge>
                @elseif($tx->status === 'partial')
                    <x-badge type="warning">Partial</x-badge>
                @else
                    <x-badge type="danger">{{ $tx->status }}</x-badge>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Email Kampus form (jika belum ada) --}}
    @if(!$student->email_kampus)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-1">Register Campus Email</h3>
        <p class="text-xs text-gray-500 mb-4">Campus email is needed for receiving schedule notifications and password reset OTP.</p>
        <form action="{{ route('student.email.send-otp') }}" method="POST">
            @csrf
            <div class="space-y-2">
                <input type="email"
                       name="email_kampus"
                       placeholder="nama@krw.horizon.ac.id"
                       required
                       class="w-full px-3 py-2.5 h-11 text-sm bg-gray-100 border border-gray-200 rounded-lg
                              text-gray-800 placeholder-gray-400
                              focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100
                              transition-colors
                              @error('email_kampus') border-red-400 @enderror">
                @error('email_kampus')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
                @if(session('warning'))
                    <p class="text-xs text-amber-600">{{ session('warning') }}</p>
                @endif
                <button type="submit"
                        class="w-full h-11 bg-primary-700 text-white text-sm font-medium rounded-lg
                               hover:bg-primary-800 transition-colors">
                    Send Verification OTP
                </button>
            </div>
        </form>
    </div>
    @endif

</x-app-layout>
