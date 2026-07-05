<x-app-layout>

    {{-- Hero: User greeting --}}
    <div class="bg-gradient-to-br from-primary-700 to-primary-900 rounded-2xl p-6 mb-5 relative overflow-hidden">
        <div class="absolute top-[-30px] right-[-30px] w-28 h-28 bg-white/10 rounded-full"></div>
        <div class="absolute bottom-[-20px] left-[-20px] w-20 h-20 bg-primary-600/40 rounded-full"></div>

        <div class="flex items-center gap-4 relative z-10">
            {{-- Avatar --}}
            <div class="w-14 h-14 bg-white/20 border-2 border-white/30 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xl font-bold uppercase">
                {{ substr($student->name, 0, 2) }}
            </div>
            <div>
                <p class="text-primary-200 text-xs">Selamat datang,</p>
                <h2 class="text-white font-bold text-base leading-tight">{{ $student->name }}</h2>
                <p class="text-primary-200 text-xs mt-0.5">{{ $student->nim }} &bull; {{ $student->studyProgram?->name ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Progress Steps --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Langkah Pengambilan Seragam</h3>
        <div class="space-y-3">
            @php
                $steps = [
                    ['label' => 'Input Ukuran Seragam', 'done' => $hasFilledSize, 'href' => route('student.sizes.index'), 'action' => 'Isi Ukuran'],
                    ['label' => 'Generate QR Identitas', 'done' => $hasQr, 'href' => route('student.qr'), 'action' => 'Lihat QR'],
                    ['label' => 'Ambil Seragam', 'done' => $recentTransactions->where('status', 'completed')->count() > 0, 'href' => null, 'action' => null],
                ];
            @endphp
            @foreach($steps as $i => $step)
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center flex-shrink-0
                            {{ $step['done'] ? 'bg-green-500 border-green-500' : ($i === 0 || $steps[$i-1]['done'] ? 'border-primary-500' : 'border-gray-300') }}">
                    @if($step['done'])
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <span class="text-xs font-bold {{ $i === 0 || $steps[$i-1]['done'] ? 'text-primary-600' : 'text-gray-400' }}">{{ $i + 1 }}</span>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm {{ $step['done'] ? 'text-gray-500 line-through' : 'text-gray-800 font-medium' }}">
                        {{ $step['label'] }}
                    </p>
                </div>
                @if(!$step['done'] && $step['href'] && ($i === 0 || $steps[$i-1]['done']))
                    <a href="{{ $step['href'] }}"
                       class="text-xs font-medium text-primary-700 bg-primary-50 px-3 py-1 rounded-full hover:bg-primary-100 transition-colors flex-shrink-0">
                        {{ $step['action'] }} →
                    </a>
                @endif
            </div>
            @if(!$loop->last)
            <div class="ml-3.5 w-px h-3 bg-gray-200"></div>
            @endif
            @endforeach
        </div>
    </div>

    {{-- Status Cards --}}
    <div class="grid grid-cols-2 gap-3 mb-5">
        {{-- Email Kampus --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Email Kampus</p>
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

        {{-- Status Pengambilan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Status</p>
            @php $latestStatus = $recentTransactions->first()?->status ?? null; @endphp
            @if($latestStatus === 'completed')
                <x-badge type="success">Selesai</x-badge>
            @elseif($latestStatus === 'partial')
                <x-badge type="warning">Sebagian</x-badge>
            @else
                <x-badge type="neutral">Belum Ambil</x-badge>
            @endif
        </div>
    </div>

    {{-- Jadwal Aktif --}}
    @if($activeSchedules->count())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Jadwal Distribusi Aktif</h3>
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
            <h3 class="text-sm font-semibold text-gray-700">Riwayat Pengambilan</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($recentTransactions as $tx)
            <div class="flex items-center justify-between px-5 py-3">
                <div>
                    <p class="text-sm text-gray-800">{{ $tx->schedule?->name ?? 'Distribusi' }}</p>
                    <p class="text-xs text-gray-400">{{ $tx->pickup_time?->format('d/m/Y H:i') ?? '-' }}</p>
                </div>
                @if($tx->status === 'completed')
                    <x-badge type="success">Selesai</x-badge>
                @elseif($tx->status === 'partial')
                    <x-badge type="warning">Sebagian</x-badge>
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
        <h3 class="text-sm font-semibold text-gray-700 mb-1">Daftarkan Email Kampus</h3>
        <p class="text-xs text-gray-500 mb-4">Email kampus dibutuhkan untuk menerima notifikasi jadwal & OTP lupa password.</p>
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
                    Kirim OTP Verifikasi
                </button>
            </div>
        </form>
    </div>
    @endif

</x-app-layout>
