<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Generate Akun Mahasiswa') }}</h2>
            <div class="flex gap-2">
                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">{{ $totalWithAccount }} Sudah</span>
                <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">{{ $totalWithoutAccount }} Belum</span>
                <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">{{ $totalStudents }} Total</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 px-4 py-3 bg-blue-100 border border-blue-300 text-blue-700 rounded-md">
                    {{ session('info') }}
                </div>
            @endif

            @if($studentsWithoutAccount->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Mahasiswa Belum Punya Akun</h3>
                        <form action="{{ route('master.student-account.generate-all') }}" method="POST" onsubmit="return confirm('Generate akun untuk semua {{ $totalWithoutAccount }} mahasiswa?')">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Generate Semua
                            </button>
                        </form>
                    </div>

                    <form action="{{ route('master.student-account.generate') }}" method="POST" id="generate-form">
                        @csrf
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            <input type="checkbox" id="select-all" class="rounded border-gray-300">
                                        </th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodi</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email Kampus</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($studentsWithoutAccount as $student)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox rounded border-gray-300">
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->nim }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $student->name }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->studyProgram?->name ?? '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->programLevel?->name ?? '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->email_kampus ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex items-center gap-3">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Generate Akun Terpilih
                            </button>
                            <span class="text-sm text-gray-500" id="selected-count">0 mahasiswa dipilih</span>
                        </div>
                    </form>

                    <div class="mt-4">
                        {{ $studentsWithoutAccount->links() }}
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center text-gray-500">
                    Semua mahasiswa sudah memiliki akun.
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked);
            updateCount();
        });

        document.querySelectorAll('.student-checkbox').forEach(cb => {
            cb.addEventListener('change', updateCount);
        });

        function updateCount() {
            const count = document.querySelectorAll('.student-checkbox:checked').length;
            document.getElementById('selected-count').textContent = count + ' mahasiswa dipilih';
        }
    </script>
    @endpush
</x-app-layout>
