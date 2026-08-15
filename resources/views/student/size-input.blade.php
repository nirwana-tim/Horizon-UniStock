<x-app-layout>
    @php
        $currentBaju = $currentSizes['baju'] ?? null;
        $currentSepatu = $currentSizes['sepatu'] ?? null;
    @endphp

    {{-- Flash Messages --}}
    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    {{-- Hero Banner --}}
    <div class="mb-6 overflow-hidden rounded-2xl h-48 relative shadow-card bg-gradient-to-br from-primary-700 to-primary-900">
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.3) 0, transparent 40%), radial-gradient(circle at 80% 70%, rgba(255,255,255,0.2) 0, transparent 35%);"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent flex items-end p-5">
            <p class="text-white font-headline-sm text-headline-sm">Lengkapi Profil Distribusi</p>
        </div>
    </div>

    {{-- Status Banner --}}
    <div id="size-status" class="mb-4">
        @if(!$canEdit)
            <div class="glass-card shadow-card rounded-2xl p-4 flex items-start gap-3">
                <span class="material-symbols-outlined text-primary mt-0.5">block</span>
                <div>
                    <p class="font-body-md text-body-md text-on-surface font-semibold">Kuota Perubahan Habis</p>
                    <p class="font-body-md text-[12px] text-secondary/60 mt-0.5">Kamu sudah memakai {{ $event->max_changes }}x perubahan pada event ini.</p>
                </div>
            </div>
        @else
            <div class="glass-card shadow-card rounded-2xl p-4 flex items-start gap-3">
                <span class="material-symbols-outlined text-primary mt-0.5">straighten</span>
                <div>
                    <p class="font-body-md text-body-md text-on-surface font-semibold">Sisa Perubahan: {{ $remainingChanges }} dari {{ $event->max_changes }}</p>
                    <p class="font-body-md text-[12px] text-secondary/60 mt-0.5">Acara: {{ $event->title }} &bull; s/d {{ $event->end_date->format('d M Y') }}</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Size Fields --}}
    <form action="{{ route('student.sizes.store') }}" method="POST" id="size-form">
        @csrf
        <input type="hidden" name="event_id" value="{{ $event->id }}">
        <input type="hidden" name="sizes[baju]" id="input-baju" value="{{ $currentBaju }}">
        <input type="hidden" name="sizes[sepatu]" id="input-sepatu" value="{{ $currentSepatu }}">

        <div class="bg-white rounded-2xl p-6 shadow-card space-y-6 animate-pop">
            {{-- Baju Field --}}
            <section>
                <label class="block font-label-md text-label-md text-secondary mb-2">UKURAN BAJU</label>
                <button type="button"
                        class="w-full flex items-center justify-between px-4 h-14 rounded-xl bg-surface-container-low active:bg-black/[0.04] transition-colors"
                        data-size-field="baju"
                        id="field-baju"
                        {{ !$canEdit ? 'disabled' : '' }}>
                    <span class="font-body-md text-body-md text-secondary/70">Pilih ukuran</span>
                    <span class="flex items-center gap-2">
                        <span id="field-baju-value" class="font-body-md text-body-md text-primary font-semibold">{{ $currentBaju ?? '—' }}</span>
                        <span class="material-symbols-outlined text-primary">chevron_right</span>
                    </span>
                </button>
                @error('sizes.baju')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </section>

            {{-- Sepatu Field --}}
            <section>
                <label class="block font-label-md text-label-md text-secondary mb-2">UKURAN SEPATU</label>
                <button type="button"
                        class="w-full flex items-center justify-between px-4 h-14 rounded-xl bg-surface-container-low active:bg-black/[0.04] transition-colors"
                        data-size-field="sepatu"
                        id="field-sepatu"
                        {{ !$canEdit ? 'disabled' : '' }}>
                    <span class="font-body-md text-body-md text-secondary/70">Pilih ukuran</span>
                    <span class="flex items-center gap-2">
                        <span id="field-sepatu-value" class="font-body-md text-body-md text-primary font-semibold">{{ $currentSepatu ?? '—' }}</span>
                        <span class="material-symbols-outlined text-primary">chevron_right</span>
                    </span>
                </button>
                @error('sizes.sepatu')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </section>
        </div>

        {{-- Submit Button --}}
        @if($canEdit)
        <div class="mt-6">
            <button type="submit"
                    class="w-full bg-primary text-white h-14 rounded-full font-headline-sm text-headline-sm shadow-button active:scale-[0.98] transition-transform duration-200 disabled:opacity-40 disabled:saturate-50"
                    id="btn-save">
                Simpan Ukuran
            </button>
        </div>
        @endif
    </form>

    {{-- Scripts --}}
    @push('scripts')
    <script>
        const sizeOptions = @json($sizeOptions);
        const selectedSizes = {
            baju: @json($currentBaju),
            sepatu: @json($currentSepatu)
        };

        // ── Size Field Click → Open Bottom Sheet ──
        document.querySelectorAll('[data-size-field]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const category = this.getAttribute('data-size-field');
                openSizeSheet(category);
            });
        });

        function sizeSheetHTML(category) {
            const opts = sizeOptions[category] || [];
            const title = category === 'baju' ? 'Ukuran Baju' : 'Ukuran Sepatu';
            return '<div class="py-2">' +
                '<h3 class="font-headline-sm text-headline-sm text-on-surface mb-4">' + title + '</h3>' +
                '<div class="space-y-1 max-h-[312px] overflow-y-auto overscroll-contain scrollbar-hide">' +
                opts.map(function(size) {
                    const active = size === selectedSizes[category]
                        ? ' bg-primary/5 text-primary font-semibold'
                        : ' text-secondary/70';
                    return '<button type="button" class="size-option w-full flex items-center justify-between px-4 py-3.5 rounded-xl hover:bg-black/[0.02] transition-colors' + active +
                        '" data-size-category="' + category + '" data-size-value="' + size +
                        '"><span class="font-body-md text-body-md">' + size +
                        '</span><span class="material-symbols-outlined text-[18px]">check</span></button>';
                }).join('') +
                '</div></div>';
        }

        function openSizeSheet(category) {
            const html = sizeSheetHTML(category);
            Alpine.store('bottomSheet').openSheet(html);

            // Bind size option clicks after sheet opens
            setTimeout(function() {
                document.querySelectorAll('.size-option[data-size-category="' + category + '"]').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const value = this.getAttribute('data-size-value');
                        selectSize(category, value);
                    });
                });
            }, 50);
        }

        function selectSize(category, value) {
            selectedSizes[category] = value;

            // Update hidden inputs
            const input = document.getElementById('input-' + category);
            if (input) input.value = value;

            // Update field display
            const fieldEl = document.getElementById('field-' + category + '-value');
            if (fieldEl) fieldEl.textContent = value;

            // Close the bottom sheet
            Alpine.store('bottomSheet').closeSheet();
        }
    </script>
    @endpush
</x-app-layout>
