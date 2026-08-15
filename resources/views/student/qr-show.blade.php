<x-app-layout>

    <main class="flex-grow px-container-margin pt-6 pb-32 flex flex-col items-center">
        {{-- QR Card --}}
        <div class="w-full max-w-sm bg-white rounded-2xl shadow-card p-8 flex flex-col items-center animate-pop">
            {{-- Student Info --}}
            <div class="flex flex-col items-center mb-6">
                <h2 class="font-headline-sm text-headline-sm text-on-surface max-w-full min-w-0 break-words text-center leading-snug">{{ $student->name }}</h2>
                <p class="font-body-md text-body-md text-secondary/60 mt-0.5">{{ $student->nim }}</p>
            </div>

            {{-- QR Code --}}
            <div class="w-full aspect-square bg-surface-container-low rounded-xl flex items-center justify-center relative mb-6 p-6 border border-black/[0.04]">
                <div class="absolute top-4 left-4 w-6 h-6 border-t-2 border-l-2 border-primary"></div>
                <div class="absolute top-4 right-4 w-6 h-6 border-t-2 border-r-2 border-primary"></div>
                <div class="absolute bottom-4 left-4 w-6 h-6 border-b-2 border-l-2 border-primary"></div>
                <div class="absolute bottom-4 right-4 w-6 h-6 border-b-2 border-r-2 border-primary"></div>
                <div class="w-full h-full bg-white rounded-xl flex items-center justify-center shadow-sm">
                    <img src="{{ $qrDataUrl }}" alt="QR {{ $student->nim }}" class="w-full h-full object-contain p-2">
                </div>
            </div>

            {{-- Download Button --}}
            <a href="{{ $qrDataUrl }}"
               download="qr-{{ $student->nim }}.png"
               class="w-full h-14 bg-primary text-white rounded-full font-headline-sm text-headline-sm flex items-center justify-center gap-2 shadow-button active:scale-[0.98] transition-transform duration-200">
                <span class="material-symbols-outlined">download</span>
                Download QR PNG
            </a>

            {{-- Info --}}
            <p class="mt-6 font-body-md text-body-md text-secondary/60 text-center leading-relaxed px-2">
                Tunjukkan QR ini pada petugas saat pengambilan paket untuk verifikasi identitas Anda.
            </p>
        </div>

        {{-- Status Cards --}}
        <div class="mt-6 grid grid-cols-2 gap-4 w-full max-w-sm">
            <div class="bg-white shadow-soft rounded-xl p-4">
                <span class="font-label-md text-label-md text-secondary/60 uppercase">Status</span>
                <p class="font-headline-sm text-headline-sm text-on-surface mt-1">Aktif</p>
            </div>
            <div class="bg-white shadow-soft rounded-xl p-4">
                <span class="font-label-md text-label-md text-secondary/60 uppercase">Valid Hingga</span>
                <p class="font-headline-sm text-headline-sm text-on-surface mt-1">{{ now()->year }}</p>
            </div>
        </div>

        {{-- Jadwal Distribusi Aktif --}}
        @if(isset($activeSchedules) && $activeSchedules->count())
        <div class="mt-6 w-full max-w-sm">
            <h3 class="font-headline-sm text-headline-sm text-on-background mb-3">Jadwal Distribusi</h3>
            <div class="bg-white shadow-card rounded-2xl overflow-hidden">
                @foreach($activeSchedules as $schedule)
                <div class="flex items-center px-5 py-4 {{ !$loop->last ? 'divider-subtle' : '' }} active:bg-black/[0.02] transition-colors cursor-pointer">
                    <div class="w-12 h-12 rounded-xl bg-primary/5 flex flex-col items-center justify-center mr-4 shrink-0">
                        <span class="text-[10px] font-bold text-primary uppercase">{{ \Carbon\Carbon::parse($schedule->date)->format('M') }}</span>
                        <span class="text-lg font-extrabold text-primary leading-tight">{{ \Carbon\Carbon::parse($schedule->date)->format('d') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-headline-sm text-[15px] text-on-background">{{ $schedule->name }}</h4>
                        <p class="font-body-md text-[13px] text-secondary/60 mt-0.5">{{ $schedule->location }}</p>
                    </div>
                    <span class="material-symbols-outlined text-primary shrink-0">chevron_right</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </main>
</x-app-layout>
