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
            <div class="mb-4">
                <h3 class="text-base font-semibold text-gray-900">{{ $student->name }}</h3>
                <p class="text-sm text-gray-500">NIM: {{ $student->nim }}</p>
                <p class="text-xs text-gray-400">{{ $student->studyProgram->name ?? '-' }} &bull; {{ $student->programLevel->name ?? '-' }}</p>
            </div>

            <div class="inline-block p-3 bg-white border border-gray-200 rounded-xl mb-5">
                <img src="{{ $qrDataUrl }}" alt="QR {{ $student->nim }}" class="w-48 h-48 md:w-64 md:h-64">
            </div>

            <div class="flex flex-col gap-3">
                <a href="{{ $qrDataUrl }}"
                   download="qr-{{ $student->nim }}.png"
                   class="w-full h-11 flex items-center justify-center bg-primary-700 text-white text-sm font-semibold rounded-lg hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-300 transition-colors">
                    Download QR (PNG)
                </a>
                <a href="{{ route('student.sizes.index') }}"
                   class="w-full h-11 flex items-center justify-center bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                    Kembali
                </a>
            </div>
        </div>
    </div>

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

    @if(isset($activeSchedules) && $activeSchedules->count())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Jadwal Distribusi Aktif</h3>
        <div class="space-y-2">
            @foreach($activeSchedules as $schedule)
            <div class="flex items-center justify-between p-3 bg-primary-50 border border-primary-100 rounded-lg">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $schedule->name }}</p>
                    <p class="text-xs text-gray-500">{{ $schedule->location }}</p>
                </div>
                <span class="text-xs font-medium text-primary-700 flex-shrink-0 ml-2">{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</x-app-layout>
