<x-app-layout>
    <main class="px-container-margin pt-6 pb-8">
        <header class="mb-7">
            <h1 id="greeting" class="font-headline-lg-mobile text-headline-lg-mobile text-on-background"></h1>
            <p class="font-body-md text-secondary/70 mt-0.5">{{ $student->nim }} &bull; {{ $student->studyProgram?->name ?? '-' }}</p>
        </header>

        <section id="email-card" class="mb-7"></section>

        <div class="space-y-7">
            <section id="jadwal-distribusi">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-headline-sm text-headline-sm text-on-background">Jadwal Distribusi</h2>
                    <button type="button" class="text-primary font-label-md hover:underline transition-transform duration-150 active:scale-95" data-scroll="jadwal-distribusi">Lihat Semua</button>
                </div>

                @if($sizeEvents->isNotEmpty())
                    <div class="bg-white shadow-card rounded-2xl overflow-hidden">
                        @foreach($sizeEvents as $event)
                            @php
                                $endDate = \Carbon\Carbon::parse($event->end_date);
                                $month = $endDate->format('M');
                                $day = $endDate->format('d');
                                $isOdd = $loop->iteration % 2 === 1;
                            @endphp

                            <div class="flex items-center px-5 py-4 {{ !$loop->last ? 'divider-subtle' : '' }} active:bg-black/[0.02] transition-colors cursor-pointer group"
                                 data-modal="schedule-{{ $loop->iteration }}"
                                 data-event-title="{{ $event->title }}"
                                 data-event-start="{{ $event->start_date->format('d M Y') }}"
                                 data-event-end="{{ $event->end_date->format('d M Y') }}"
                                 data-event-location="{{ $event->description ?? '' }}"
                                 data-event-note="">
                                <div class="w-12 h-12 rounded-xl {{ $isOdd ? 'bg-primary/5' : 'bg-surface-variant/50' }} flex flex-col items-center justify-center mr-4 shrink-0">
                                    <span class="text-[10px] font-bold {{ $isOdd ? 'text-primary' : 'text-secondary' }} uppercase">{{ $month }}</span>
                                    <span class="text-lg font-extrabold {{ $isOdd ? 'text-primary' : 'text-secondary' }} leading-tight">{{ $day }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-headline-sm text-[15px] text-on-background">{{ $event->title }}</h4>
                                    <p class="font-body-md text-[13px] text-secondary/60 mt-0.5">{{ $event->description ?? $event->start_date->format('d M') . ' — ' . $event->end_date->format('d M Y') }}</p>
                                </div>
                                <span class="material-symbols-outlined text-primary shrink-0 group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </main>

    @push('scripts')
    <script>
        (function() {
            var hour = new Date().getHours();
            var greeting;
            if (hour >= 3 && hour < 12) {
                greeting = 'Selamat Pagi';
            } else if (hour >= 12 && hour < 15) {
                greeting = 'Selamat Siang';
            } else if (hour >= 15 && hour < 21) {
                greeting = 'Selamat Sore';
            } else {
                greeting = 'Selamat Malam';
            }
            var fullName = '{{ $student->name }}';
            var firstName = fullName.trim().split(/\s+/)[0];
            document.getElementById('greeting').textContent = greeting + ', ' + firstName + '!';
        })();
    </script>
    @endpush
</x-app-layout>
