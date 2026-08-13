<x-app-layout>

    <x-page-header title="Generate Account" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Stats --}}
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <x-badge type="success">{{ $totalWithAccount }} Has Account</x-badge>
                        <x-badge type="warning">{{ $totalWithoutAccount }} No Account</x-badge>
                        <x-badge type="neutral">{{ $totalStudents }} Total</x-badge>
                        <a href="{{ route('students.credentials') }}"
                           class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800">
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Distribusi Kredensial
                        </a>
                    </div>

                    @if($totalWithoutAccount > 0)
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-700">Students Without Accounts</h3>
                            <form action="{{ route('students.generateAll') }}" method="POST" onsubmit="return confirm('Generate accounts for all {{ $totalWithoutAccount }} students?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800">
                                        Generate All
                                </button>
                            </form>
                        </div>

                        <form action="{{ route('students.generate') }}" method="POST" id="generate-form" x-data="{ search: '' }">
                            @csrf

                            <div class="relative mb-4">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text"
                                       x-model.debounce.300ms="search"
                                       placeholder="Cari NIM, nama, email, atau program studi..."
                                       class="block w-72 pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                <input type="checkbox" id="select-all" class="rounded border-gray-300">
                                            </th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Study Program</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Generation</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campus Email</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($studentsWithoutAccount as $student)
                                        <tr class="hover:bg-gray-50"
                                            x-show="search === '' ||
                                                    '{{ $student->nim }}'.toLowerCase().includes(search.toLowerCase()) ||
                                                    '{{ $student->name }}'.toLowerCase().includes(search.toLowerCase()) ||
                                                    '{{ $student->studyProgram?->name ?? '' }}'.toLowerCase().includes(search.toLowerCase()) ||
                                                    '{{ $student->email_kampus ?? '' }}'.toLowerCase().includes(search.toLowerCase())">
                                            <td class="px-3 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox rounded border-gray-300">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->nim }}</td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $student->name }}</td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->studyProgram?->name ?? '-' }}</td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->generation?->label ?? '-' }}</td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->email_kampus ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 flex items-center gap-3">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800">
                                    Generate Selected Accounts
                                </button>
                                <span class="text-sm text-gray-500" id="selected-count">0 students selected</span>
                            </div>
                        </form>

                        <div class="mt-4">
                            {{ $studentsWithoutAccount->links() }}
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-8">
                            All students already have accounts.
                        </div>
                    @endif

                    {{-- Accounts pending password change --}}
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4">
                            Sudah Memiliki Akun (Menunggu Ganti Password)
                        </h3>

                        @if ($studentsPending->isEmpty())
                            <div class="text-center text-gray-500 py-6">
                                Tidak ada akun yang menunggu ganti password. Semua mahasiswa sudah menggunakan password baru.
                            </div>
                        @else
                            <div x-data="{ search: '' }" class="relative mb-4">
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

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Study Program</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Log In</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($studentsPending as $student)
                                        <tr class="hover:bg-gray-50"
                                            x-show="search === '' ||
                                                    '{{ $student->nim }}'.toLowerCase().includes(search.toLowerCase()) ||
                                                    '{{ $student->name }}'.toLowerCase().includes(search.toLowerCase()) ||
                                                    '{{ $student->studyProgram?->name ?? '' }}'.toLowerCase().includes(search.toLowerCase())">
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
                                            <td class="px-3 py-4 whitespace-nowrap text-sm">
                                                @php $email = $student->latest_email; @endphp
                                                @if ($email)
                                                    @switch($email->status)
                                                        @case('sent') <x-badge type="success">Terkirim</x-badge> @break
                                                        @case('failed') <x-badge type="danger">Gagal</x-badge> @break
                                                        @case('skipped') <x-badge type="neutral">Lewati</x-badge> @break
                                                        @default <x-badge type="warning">Menunggu</x-badge>
                                                    @endswitch
                                                @else
                                                    <x-badge type="neutral">Belum Kirim</x-badge>
                                                @endif
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
    </div>

    @push('scripts')
    <script>
        document.getElementById('select-all')?.addEventListener('change', function() {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked);
            updateCount();
        });

        document.querySelectorAll('.student-checkbox').forEach(cb => {
            cb.addEventListener('change', updateCount);
        });

        function updateCount() {
            const count = document.querySelectorAll('.student-checkbox:checked').length;
            document.getElementById('selected-count').textContent = count + ' students selected';
        }
    </script>
    @endpush

</x-app-layout>
