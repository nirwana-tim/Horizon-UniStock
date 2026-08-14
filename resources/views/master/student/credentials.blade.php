<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Distribusi Kredensial') }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('students.generate-index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                    {{ __('Generate Akun') }}
                </a>
                <a href="{{ route('students.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                    {{ __('Master Mahasiswa') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <x-alert type="success">{{ session('success') }}</x-alert>
                    @endif
                    @if (session('error'))
                        <x-alert type="error">{{ session('error') }}</x-alert>
                    @endif
                    @if (session('warning'))
                        <x-alert type="warning">{{ session('warning') }}</x-alert>
                    @endif
                    @if (session('info'))
                        <x-alert type="info">{{ session('info') }}</x-alert>
                    @endif

                    {{-- Stats --}}
                    <div class="flex flex-wrap gap-2 mb-4">
                        <x-badge type="primary">{{ $totalWithAccount }} Punya Akun</x-badge>
                        <x-badge type="warning">{{ $students->count() }} Belum Ganti Password</x-badge>
                        <x-badge type="success">{{ $totalWithoutAccount }} Belum Punya Akun</x-badge>
                        <x-badge type="neutral">{{ $totalStudents }} Total</x-badge>
                    </div>

                    {{-- SMTP banner --}}
                    @if (! \App\Models\SmtpSetting::isActiveConfigured())
                        <x-alert type="warning">
                            <strong>SMTP belum dikonfigurasi oleh Super Admin.</strong>
                            Fitur email tidak tersedia. Gunakan tombol <strong>Copy</strong> atau
                            <strong>Download Excel</strong> untuk mendistribusikan kredensial.
                            Super Admin dapat mengatur SMTP di halaman Pengaturan Mail.
                        </x-alert>
                    @endif

                    {{-- Top actions --}}
                    <div class="flex flex-wrap items-center gap-3 mb-6">
                        <a href="{{ route('students.credentials.export') }}"
                           class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800">
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Excel
                        </a>

                        @if (\App\Models\SmtpSetting::isActiveConfigured())
                            <form action="{{ route('students.credentials.resend-all-failed') }}" method="POST"
                                  onsubmit="return confirm('Kirim ulang email kredensial untuk semua mahasiswa yang tersedia?')">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Kirim Ulang Semua
                                </button>
                            </form>
                        @endif

                        @if (! empty($passwords))
                            <form action="{{ route('students.credentials.destroy') }}" method="POST"
                                  onsubmit="return confirm('Hapus semua password sementara dari sesi ini?')">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Bersihkan Sesi
                                </button>
                            </form>
                        @endif
                    </div>

                    @if ($students->isEmpty())
                        <div class="text-center text-gray-500 py-8">
                            <p class="text-lg">Tidak ada akun yang menunggu ganti password.</p>
                            <p class="text-sm text-gray-400 mt-1">
                                Password mahasiswa yang sudah mengganti password otomatis hilang dari daftar ini.
                            </p>
                        </div>
                    @else
                        <div x-data="{ search: '' }" class="mb-4">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text"
                                       x-model.debounce.300ms="search"
                                       placeholder="Cari NIM, nama, atau program studi..."
                                       class="block w-72 pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Study Program</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Log In</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Password</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($students as $student)
                                    <tr class="hover:bg-gray-50"
                                        x-show="search === '' ||
                                                @js($student->nim).toLowerCase().includes(search.toLowerCase()) ||
                                                @js($student->name).toLowerCase().includes(search.toLowerCase()) ||
                                                @js($student->studyProgram?->name ?? '').toLowerCase().includes(search.toLowerCase())">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->nim }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $student->name }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->studyProgram?->name ?? '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap">
                                            @if ($student->user?->last_login_at)
                                                <x-badge type="info">Sudah Login</x-badge>
                                            @else
                                                <x-badge type="warning">Belum Login</x-badge>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">
                                            @if ($student->temp_password)
                                                <span class="font-mono" data-password="{{ $student->nim }}">••••••••••••</span>
                                                <button type="button" onclick="revealPassword(this, '{{ $student->nim }}')"
                                                        class="ml-1 p-1 text-gray-400 hover:text-primary-700" title="Lihat">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <button type="button" onclick="copyPassword(this, '{{ $student->nim }}')"
                                                        class="ml-1 p-1 text-gray-400 hover:text-primary-700" title="Salin">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $email = $student->latest_email;
                                            @endphp
                                            @if ($email)
                                                @switch($email->status)
                                                    @case('sent')
                                                        <x-badge type="success">Terkirim</x-badge>
                                                        @break
                                                    @case('failed')
                                                        <x-badge type="danger">Gagal</x-badge>
                                                        @break
                                                    @case('skipped')
                                                        <x-badge type="neutral">Lewati</x-badge>
                                                        @break
                                                    @default
                                                        <x-badge type="warning">Menunggu</x-badge>
                                                @endswitch
                                            @else
                                                <x-badge type="neutral">Belum Kirim</x-badge>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-1">
                                                <form action="{{ route('students.credentials.reset-password', $student) }}" method="POST"
                                                      onsubmit="return confirm('Reset password untuk {{ $student->name }} ({{ $student->nim }})?')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center px-2.5 py-1 bg-amber-100 text-amber-800 rounded-md text-xs font-medium hover:bg-amber-200">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                        </svg>
                                                        Reset
                                                    </button>
                                                </form>

                                                @if (\App\Models\SmtpSetting::isActiveConfigured() && $student->temp_password)
                                                    <form action="{{ route('students.credentials.resend-email', $student) }}" method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                                class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-800 rounded-md text-xs font-medium hover:bg-green-200">
                                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                            </svg>
                                                            Kirim
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const ICON_EYE = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>';

        const ICON_EYE_SLASH = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>';

        const ICON_CHECK = '<svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>';

        const passwordCache = {};

        function fetchPassword(nim) {
            if (passwordCache[nim]) {
                return Promise.resolve(passwordCache[nim]);
            }
            return fetch('{{ route('students.credentials.password', ['student' => '_NIM_']) }}'.replace('_NIM_', nim), {
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                passwordCache[nim] = data.password || '';
                return passwordCache[nim];
            })
            .catch(() => '');
        }

        function revealPassword(btn, nim) {
            const cell = btn.closest('td');
            const span = cell.querySelector('span[data-password]');
            if (span.textContent.includes('•')) {
                fetchPassword(nim).then(password => {
                    span.textContent = password;
                    btn.innerHTML = ICON_EYE_SLASH;
                    btn.title = 'Sembunyikan';
                });
            } else {
                span.textContent = '••••••••••••';
                btn.innerHTML = ICON_EYE;
                btn.title = 'Lihat';
            }
        }

        function copyPassword(btn, nim) {
            fetchPassword(nim).then(password => {
                if (!password) return;
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(password).then(() => { flashCopy(btn); });
                } else {
                    const ta = document.createElement('textarea');
                    ta.value = password;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    flashCopy(btn);
                }
            });
        }

        function flashCopy(btn) {
            const original = btn.innerHTML;
            btn.innerHTML = ICON_CHECK;
            btn.title = 'Tersalin';
            setTimeout(() => { btn.innerHTML = original; btn.title = 'Salin'; }, 1200);
        }
    </script>
    @endpush

</x-app-layout>
