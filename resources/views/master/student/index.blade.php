<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {!! session('success') !!}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 px-4 py-3 bg-blue-100 border border-blue-300 text-blue-700 rounded-md">
                    {{ session('info') }}
                </div>
            @endif

            <div x-data="{ activeTab: '{{ request('tab') === 'generate-akun' ? 'generate' : 'data' }}' }">
                {{-- Tab Header --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="flex items-center border-b border-gray-200 px-6 pt-4 gap-2">
                        <button @click="activeTab = 'data'"
                            :class="activeTab === 'data' ? 'border-primary-700 text-primary-700 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2.5 text-sm border-b-2 -mb-px transition-colors">
                            Data Mahasiswa
                        </button>
                        <button @click="activeTab = 'generate'"
                            :class="activeTab === 'generate' ? 'border-primary-700 text-primary-700 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2.5 text-sm border-b-2 -mb-px transition-colors">
                            Generate Akun
                            @if($totalWithoutAccount > 0)
                                <span class="ml-1.5 inline-flex items-center justify-center w-5 h-5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">{{ $totalWithoutAccount }}</span>
                            @endif
                        </button>

                        <div class="ml-auto flex gap-2">
                            <template x-if="activeTab === 'data'">
                                <a href="{{ route('students.create') }}" class="inline-flex items-center px-4 py-2 bg-[#980416] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#7a0311] transition">
                                    {{ __('Tambah Mahasiswa') }}
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Tab: Data Mahasiswa --}}
                <div x-show="activeTab === 'data'">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program Studi</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akun</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($students as $index => $student)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $students->firstItem() + $index }}</td>
                                                <td class="px-4 py-3 text-sm font-mono text-gray-900">{{ $student->nim }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $student->name }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $student->studyProgram->name ?? '-' }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $student->programLevel->name ?? '-' }}</td>
                                                <td class="px-4 py-3 text-sm">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $student->student_type === 'freshman' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ ucfirst($student->student_type) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    @if($student->user_id)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Belum</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm text-right space-x-1.5">
                                                     <a href="{{ route('students.show', $student) }}" class="inline-flex items-center px-2.5 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Lihat</a>
                                                    <x-delete-modal
                                                        :route="route('students.destroy', $student)"
                                                        label="Hapus Mahasiswa"
                                                        description="Apakah Anda yakin ingin menghapus data mahasiswa {{ $student->name }}? Data ini tidak dapat dikembalikan."
                                                    />
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="px-4 py-4 text-center text-sm text-gray-500">Belum ada data mahasiswa.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $students->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab: Generate Akun --}}
                <div x-show="activeTab === 'generate'" x-cloak>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            {{-- Stats --}}
                            <div class="flex gap-2 mb-4">
                                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">{{ $totalWithAccount }} Sudah</span>
                                <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">{{ $totalWithoutAccount }} Belum</span>
                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">{{ $totalStudents }} Total</span>
                            </div>

                            @if($totalWithoutAccount > 0)
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-semibold text-gray-700">Mahasiswa Belum Punya Akun</h3>
                                    <form action="{{ route('students.generateAll') }}" method="POST" onsubmit="return confirm('Generate akun untuk semua {{ $totalWithoutAccount }} mahasiswa?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800">
                                            Generate Semua
                                        </button>
                                    </form>
                                </div>

                                <form action="{{ route('students.generate') }}" method="POST" id="generate-form">
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
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800">
                                            Generate Akun Terpilih
                                        </button>
                                        <span class="text-sm text-gray-500" id="selected-count">0 mahasiswa dipilih</span>
                                    </div>
                                </form>

                                <div class="mt-4">
                                    {{ $studentsWithoutAccount->links() }}
                                </div>
                            @else
                                <div class="text-center text-gray-500 py-8">
                                    Semua mahasiswa sudah memiliki akun.
                                </div>
                            @endif
                        </div>
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
            document.getElementById('selected-count').textContent = count + ' mahasiswa dipilih';
        }
    </script>
    @endpush
</x-app-layout>
