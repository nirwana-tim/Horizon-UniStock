<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Scan QR / Search Student') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 flex items-center gap-4">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span><strong>{{ $staff->name }}</strong></span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Today: <strong>{{ $todayCount }}</strong> distributions</span>
                </div>
            </div>

            @if($activeSchedule)
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                    <p class="text-sm text-blue-700">
                        <strong>Active Schedule:</strong> {{ $activeSchedule->name }}<br>
                        <strong>Location:</strong> {{ $activeSchedule->location }}<br>
                        <strong>Session:</strong> {{ $activeSchedule->session }}
                    </p>
                </div>
            @else
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-yellow-700">
                        <strong>Warning:</strong> No active distribution schedule for today.
                    </p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Scan QR Code</h3>
                        <div id="reader" class="w-full rounded-lg overflow-hidden border border-gray-200" style="min-height: 300px;"></div>
                        <div id="scan-result" class="mt-4"></div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Manual Search (NIM)</h3>

                        <div x-data="{
                            query: '',
                            searching: false,
                            error: '',
                            doSearch() {
                                if (!this.query.trim()) return;
                                this.searching = true;
                                this.error = '';
                                axios.post('{{ route('distribution.search') }}', {
                                    query: this.query,
                                    _token: '{{ csrf_token() }}'
                                }).then(r => {
                                    if (r.data.found) {
                                        window.location.href = r.data.redirect;
                                    } else {
                                        this.error = r.data.message;
                                        this.searching = false;
                                    }
                                }).catch(() => {
                                    this.error = 'Terjadi kesalahan. Silakan coba lagi.';
                                    this.searching = false;
                                });
                            }
                        }">
                            <div>
                                <label for="query" class="block text-sm font-medium text-gray-700">Student NIM</label>
                                <div class="relative mt-1">
                                    <input type="text" x-model="query" id="query"
                                        @input.debounce.400ms="if(query.length >= 3) doSearch()"
                                        @keydown.enter.prevent="doSearch()"
                                        placeholder="Enter student NIM..."
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm pr-10">
                                    <div x-show="searching" class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <svg class="animate-spin h-4 w-4 text-primary-700" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p x-show="error" x-text="error" class="mt-1 text-sm text-red-600"></p>
                            </div>
                            <div class="mt-4">
                                <button type="button" @click="doSearch()"
                                    class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:bg-primary-800 disabled:opacity-50 transition ease-in-out duration-150"
                                    :disabled="searching || !query.trim()">
                                    <span x-show="searching" class="mr-2">
                                        <svg class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </span>
                                    {{ __('Search Student') }}
                                </button>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
                            <p class="text-xs text-gray-500">
<strong>Instructions:</strong><br>
                                 - Point camera at student's QR Code<br>
                                 - Or type NIM manually in the field above (min 3 chars)<br>
                                 - QR Code is permanent (once in a lifetime)
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            if (typeof Html5Qrcode === 'undefined') {
                console.warn('Html5Qrcode library not loaded. QR scanning disabled.');
                document.getElementById('reader').innerHTML =
                    '<div class="p-4 text-center text-gray-500">QR Scanner not available. Use manual NIM search.</div>';
                return;
            }

            const html5QrCode = new Html5Qrcode("reader");

            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                function onScanSuccess(decodedText) {
                    document.getElementById('scan-result').innerHTML =
                        `<div class="p-4 bg-green-50 border border-green-200 rounded-md">
<p class="text-sm text-green-700">QR detected: <strong>${decodedText}</strong></p>
                             <p class="text-xs text-green-600 mt-1">Searching for student data...</p>
                        </div>`;

                    html5QrCode.stop().then(() => {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("distribution.search") }}';

                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'query';
                        input.value = decodedText;
                        form.appendChild(input);

                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';
                        form.appendChild(csrfInput);

                        document.body.appendChild(form);
                        form.submit();
                    }).catch(err => {
                        console.error('Failed to stop scanner:', err);
                        window.location.href = `{{ url('distribution/student') }}/${encodeURIComponent(decodedText)}`;
                    });
                },
                function onScanFailure(error) {
                    // Silence scan failures - continuous scanning
                }
            ).catch(err => {
                document.getElementById('reader').innerHTML =
                    '<div class="p-4 text-center text-red-500">Failed to access camera. Use manual NIM search.</div>';
            });
        });
    </script>
    @endpush
</x-app-layout>

