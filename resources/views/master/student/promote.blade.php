<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="serverTable('{{ route('students.promote.form') }}')">
                <x-page-header title="Promote Students" />

                @if(session('success'))
                    <x-alert type="success">{{ session('success') }}</x-alert>
                @endif

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form action="{{ route('students.promote') }}" method="POST" id="promote-form">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <x-input-label value="Target Generation (Opsional)" />
                                    <select name="target_level_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                        <option value="">-- Otomatis (Naikkan Semester) --</option>
                                        @foreach($generations as $level)
                                            <option value="{{ $level->id }}">{{ $level->label }} ({{ $level->code }})</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('target_level_id')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label value="Target Program Studi (Opsional)" />
                                    <select name="target_study_program_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                        <option value="">-- Tetap di Prodi Saat Ini --</option>
                                        @foreach($studyPrograms as $program)
                                            <option value="{{ $program->id }}">{{ $program->faculty->code }} - {{ $program->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('target_study_program_id')" class="mt-1" />
                                </div>
                            </div>

                            <div class="mb-4 flex items-center gap-4">
                                <input type="text"
                                    x-model="search"
                                    @input.debounce.300ms="page=1; fetchData()"
                                    placeholder="Cari berdasarkan nama atau NIM..."
                                    class="w-72 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                <span class="text-sm text-gray-500" id="selected-count">0 mahasiswa dipilih</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                <input type="checkbox" id="select-all" class="rounded border-gray-300">
                                            </th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Lengkap</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program Studi</th>
<th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Generation</th>
<th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Level</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($students as $student)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox rounded border-gray-300">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $student->nim }}</td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $student->name }}</td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->studyProgram?->name ?? '-' }}</td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->generation?->label ?? '-' }}</td>
                                            <td class="px-3 py-4 whitespace-nowrap">
                                                <x-badge type="info">{{ $student->student_level_label }}</x-badge>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">
                                                Tidak ada mahasiswa ditemukan.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $students->links() }}
                            </div>

                            <div class="mt-6 flex items-center gap-3">
                                <button type="submit"
                                    onclick="return confirm('Promosi semester untuk mahasiswa terpilih (misal: Year 1 Sem 1 → Year 1 Sem 2)?')"
                                    class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 transition">
                                    Promosikan Mahasiswa Terpilih
                                </button>
                                <a href="{{ route('students.index') }}"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                                    Batal
                                </a>
                            </div>
                        </form>
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
