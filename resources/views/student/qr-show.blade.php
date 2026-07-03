<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('QR Code Identitas') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $student->name }}</h3>
                            <p class="text-gray-600">NIM: {{ $student->nim }}</p>
                            <p class="text-sm text-gray-500">{{ $student->studyProgram->name ?? '-' }} | {{ $student->programLevel->name ?? '-' }}</p>
                        </div>

                        <div class="mb-6 inline-block p-4 bg-white border-2 border-gray-200 rounded-lg" id="qr-container">
                            {!! QrCode::size(250)
                                ->color(38, 38, 38)
                                ->backgroundColor(255, 255, 255)
                                ->generate($student->qr_token) !!}
                        </div>

                        <div class="mb-6">
                            <p class="text-xs text-gray-400">Token: {{ substr($student->qr_token, 0, 8) }}...</p>
                            <p class="text-xs text-gray-400">Dibuat: {{ $student->qr_generated_at->format('d M Y H:i') }}</p>
                        </div>

                        <div class="flex items-center justify-center gap-4">
                            <a href="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(500)->color(38,38,38)->generate($student->qr_token)) }}"
                               download="qr-{{ $student->nim }}.svg"
                               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Download QR (SVG)') }}
                            </a>
                            <a href="{{ route('student.sizes.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Kembali') }}
                            </a>
                        </div>

                        <div class="mt-8 p-4 bg-amber-50 border border-amber-200 rounded-md text-left">
                            <p class="text-sm text-amber-700">
                                <strong>Petunjuk:</strong>
                            </p>
                            <ul class="mt-2 text-sm text-amber-600 list-disc list-inside space-y-1">
                                <li>Tunjukkan QR Code ini kepada petugas saat pengambilan seragam.</li>
                                <li>QR Code ini bersifat permanen dan berlaku seumur hidup.</li>
                                <li>Jangan bagikan QR Code Anda kepada orang lain.</li>
                            </ul>
                        </div>

                        @if(isset($activeSchedules) && $activeSchedules->count())
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-md text-left">
                            <p class="text-sm text-blue-700 font-semibold mb-3">Jadwal Distribusi Aktif</p>
                            <div class="space-y-2">
                                @foreach($activeSchedules as $schedule)
                                <div class="flex items-center justify-between p-2 bg-white rounded border border-blue-100">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $schedule->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $schedule->stage?->name ?? '-' }} | {{ $schedule->location }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function downloadQR() {
            const svg = document.querySelector('#qr-container svg');
            const svgData = new XMLSerializer().serializeToString(svg);
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();

            img.onload = function() {
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);

                const link = document.createElement('a');
                link.download = 'qr-{{ $student->nim }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            };

            img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
        }
    </script>
    @endpush
</x-app-layout>
