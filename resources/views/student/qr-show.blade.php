<x-app-layout>
    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800">QR Code Identitas</h2>
        <p class="text-xs text-gray-500 mt-0.5">Tunjukkan QR ini saat pengambilan seragam</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-5">
        <div class="text-center">
            {{-- Info Student --}}
            <div class="mb-5">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-xl font-bold text-primary-700 uppercase">{{ substr($student->name, 0, 2) }}</span>
                </div>
                <h3 class="text-base font-bold text-gray-900">{{ $student->name }}</h3>
                <p class="text-sm text-gray-500 mt-0.5">NIM: <span class="font-mono font-medium text-gray-700">{{ $student->nim }}</span></p>
                <p class="text-xs text-gray-400 mt-1">{{ $student->studyProgram->name ?? '-' }} &bull; {{ $student->generation->label ?? '-' }}</p>
            </div>

            {{-- QR Code --}}
            <div class="inline-block p-4 bg-white rounded-xl mb-5 relative"
                 style="box-shadow: 0 0 0 2px rgba(152, 4, 22, 0.1), 0 0 20px rgba(152, 4, 22, 0.06);">
                <div class="absolute inset-0 rounded-xl border-2 border-primary-200 opacity-50"></div>
                <img src="{{ $qrDataUrl }}" alt="QR {{ $student->nim }}" class="relative w-48 h-48 md:w-56 md:h-56">
            </div>

            {{-- Tombol --}}
            <div class="flex flex-col gap-3">
                <a href="{{ $qrDataUrl }}"
                   download="qr-{{ $student->nim }}.png"
                   class="w-full h-11 flex items-center justify-center gap-2 bg-primary-700 text-white text-sm font-semibold rounded-lg hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download QR (PNG)
                </a>
                <a href="{{ route('student.items.index') }}"
                   class="w-full h-11 flex items-center justify-center gap-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    Lihat Item Seragam
                </a>
            </div>
        </div>
    </div>

    {{-- Petunjuk --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-amber-800">Petunjuk:</p>
                <ul class="mt-1 text-xs text-amber-700 space-y-1 list-disc list-inside">
                    <li>Tunjukkan QR Code ini ke petugas saat pengambilan seragam.</li>
                    <li>QR Code ini berisi NIM kamu dan bersifat permanen.</li>
                    <li>Jangan bagikan QR Code kamu ke orang lain.</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Jadwal Distribusi Aktif --}}
    @if(isset($activeSchedules) && $activeSchedules->count())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <div class="px-5 py-3.5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Jadwal Distribusi Aktif</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($activeSchedules as $schedule)
            <div class="flex items-center gap-3 px-5 py-3.5">
                <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $schedule->name }}</p>
                    <p class="text-xs text-gray-500">{{ $schedule->location }}</p>
                </div>
                <span class="text-xs font-medium text-primary-700 flex-shrink-0 whitespace-nowrap">{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</x-app-layout>
