<x-app-layout>
    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    @if(session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800">Pilih Event Pengisian Ukuran</h2>
        <p class="text-xs text-gray-500 mt-0.5">Pilih event untuk mengisi ukuran seragam kamu</p>
    </div>

    @if($events->isEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-center">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-10 h-10 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-amber-700">Belum ada event pengisian ukuran</p>
                    <p class="text-xs text-amber-600 mt-1">Saat ini belum ada event pengisian ukuran yang aktif. Silakan hubungi admin untuk informasi lebih lanjut.</p>
                </div>
            </div>
        </div>
    @else
        <div class="space-y-3">
            @foreach($events as $event)
                @php
                    $profile = $student->activeSizeProfile;
                    $filledCount = $profile ? $profile->sizeItems->count() : 0;
                    $sub = $submissions->get($event->id);
                    $subCount = $sub?->submission_count ?? 0;
                    $remaining = $event->max_changes - $subCount;
                    $isMaxed = $remaining <= 0;
                @endphp

                <a href="{{ route('student.sizes.input', $event) }}"
                   class="block bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:border-primary-300 hover:shadow-md transition-all active:scale-[0.99]">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-gray-800">{{ $event->title }}</h3>
                            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-500">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $event->start_date->format('d M Y') }} — {{ $event->end_date->format('d M Y H:i') }}
                                </span>
                                @if($event->max_changes > 0)
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        @if($isMaxed)
                                            Selesai ({{ $subCount }}/{{ $event->max_changes }})
                                        @else
                                            Sisa {{ $remaining }}x pengisian
                                        @endif
                                    </span>
                                @endif
                            </div>
                            @if($event->description)
                                <p class="text-xs text-gray-400 mt-1.5 line-clamp-2">{{ $event->description }}</p>
                            @endif
                        </div>
                        <div class="flex-shrink-0 self-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-app-layout>
