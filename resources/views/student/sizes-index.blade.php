<x-app-layout>
    @php
        $hour = now()->format('H');
        if ($hour < 12) $greeting = 'Selamat Pagi';
        elseif ($hour < 15) $greeting = 'Selamat Siang';
        elseif ($hour < 18) $greeting = 'Selamat Sore';
        else $greeting = 'Selamat Malam';

        $currentBaju = $profile?->baju_size ?? null;
        $currentSepatu = $profile?->sepatu_size ?? null;
    @endphp

    <main class="flex-1 flex flex-col">
        <div class="max-w-md mx-auto">
            {{-- Hero Banner --}}
            <div class="mb-6 overflow-hidden rounded-2xl h-48 relative shadow-card bg-gradient-to-br from-primary-700 to-primary-900">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.3) 0, transparent 40%), radial-gradient(circle at 80% 70%, rgba(255,255,255,0.2) 0, transparent 35%);"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent flex items-end p-5">
                    <p class="text-white font-headline-sm text-headline-sm">Lengkapi Profil Distribusi</p>
                </div>
            </div>

            {{-- Current Sizes --}}
            <div id="size-status" class="mb-4">
                @if($currentBaju || $currentSepatu)
                    <div class="glass-card shadow-card rounded-2xl p-4 flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5">checkroom</span>
                        <div>
                            <p class="font-body-md text-body-md text-on-surface font-semibold">
                                @if($currentBaju && $currentSepatu)
                                    Ukuran: {{ $currentBaju }} / {{ $currentSepatu }}
                                @elseif($currentBaju)
                                    Baju: {{ $currentBaju }}
                                @else
                                    Sepatu: {{ $currentSepatu }}
                                @endif
                            </p>
                            <p class="font-body-md text-[12px] text-secondary/60 mt-0.5">Ukuran baju dan sepatu kamu saat ini.</p>
                        </div>
                    </div>
                @else
                    <div class="glass-card shadow-card rounded-2xl p-4 flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5">info</span>
                        <div>
                            <p class="font-body-md text-body-md text-on-surface font-semibold">Belum Ada Ukuran</p>
                            <p class="font-body-md text-[12px] text-secondary/60 mt-0.5">Pilih ukuran baju dan sepatu dari event yang tersedia.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Events --}}
            <section>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-headline-sm text-headline-sm text-on-background">Jadwal Distribusi</h2>
                </div>

                @if($events->isEmpty())
                    {{-- Empty State --}}
                    <div class="bg-white shadow-card rounded-2xl p-6 text-center">
                        <span class="material-symbols-outlined text-4xl text-secondary/30 mb-3">event_busy</span>
                        <p class="font-body-md text-body-md text-secondary/50">Tidak ada event aktif saat ini</p>
                        <p class="font-body-md text-[12px] text-secondary/40 mt-1">Silakan hubungi admin untuk informasi lebih lanjut.</p>
                    </div>
                @else
                    <div class="bg-white shadow-card rounded-2xl overflow-hidden">
                        @foreach($events as $event)
                            @php
                                $sub = $submissions->get($event->id);
                                $subCount = $sub?->submission_count ?? 0;
                                $remaining = $event->max_changes - $subCount;
                                $isMaxed = $remaining <= 0;

                                $endDate = \Carbon\Carbon::parse($event->end_date);
                                $month = $endDate->format('M');
                                $day = $endDate->format('d');
                            @endphp

                            <a href="{{ route('student.sizes.input', $event) }}"
                               class="flex items-center px-5 py-4 {{ !$loop->last ? 'divider-subtle' : '' }} active:bg-black/[0.02] transition-colors cursor-pointer group">
                                {{-- Date Badge --}}
                                <div class="w-12 h-12 rounded-xl {{ $isMaxed ? 'bg-surface-variant/50' : 'bg-primary/5' }} flex flex-col items-center justify-center mr-4 shrink-0">
                                    <span class="text-[10px] font-bold {{ $isMaxed ? 'text-secondary' : 'text-primary' }} uppercase">{{ $month }}</span>
                                    <span class="text-lg font-extrabold {{ $isMaxed ? 'text-secondary' : 'text-primary' }} leading-tight">{{ $day }}</span>
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-headline-sm text-[15px] text-on-background">{{ $event->title }}</h4>
                                    <p class="font-body-md text-[13px] text-secondary/60 mt-0.5">
                                        {{ $event->start_date->format('d M') }} — {{ $event->end_date->format('d M Y') }}
                                    </p>
                                    @if($event->max_changes > 0)
                                        <p class="font-body-md text-[12px] text-secondary/50 mt-1">
                                            @if($isMaxed)
                                                <span class="text-secondary/70">Selesai ({{ $subCount }}/{{ $event->max_changes }})</span>
                                            @else
                                                <span class="text-primary">Sisa {{ $remaining }}x pengisian</span>
                                            @endif
                                        </p>
                                    @endif
                                </div>

                                {{-- Chevron --}}
                                <span class="material-symbols-outlined text-primary shrink-0 group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </main>
</x-app-layout>
