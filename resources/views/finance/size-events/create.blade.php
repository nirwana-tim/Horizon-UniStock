<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-page-header title="Buat Event Ganti / Pengisian Ukuran Baju" />

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <form action="{{ route('distribution.size-events.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label value="Judul / Nama Event *" />
                        <input type="text" name="title" required value="{{ old('title') }}"
                            placeholder="Contoh: Event Buka Edit Ukuran Angkatan 2024 - Semester 1"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <x-input-error :messages="$errors->get('title')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label value="Deskripsi / Catatan" />
                        <textarea name="description" rows="2"
                            placeholder="Catatan untuk mahasiswa atau internal admin..."
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Waktu Mulai *" />
                            <input type="datetime-local" name="start_date" required value="{{ old('start_date', now()->format('Y-m-d\TH:i')) }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            <x-input-error :messages="$errors->get('start_date')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label value="Batas Akhir / Deadline *" />
                            <input type="datetime-local" name="end_date" required value="{{ old('end_date', now()->addDays(7)->format('Y-m-d\TH:i')) }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            <x-input-error :messages="$errors->get('end_date')" class="mt-1" />
                        </div>
                    </div>

                    <hr class="my-4 border-gray-200">

                    <h4 class="text-sm font-semibold text-gray-800">Target Filter Mahasiswa (Kosongkan jika untuk semua):</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Target Tipe Mahasiswa / Semester" />
                            <select name="student_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                <option value="">-- Semua Semester / Tipe --</option>
                                @foreach($studentLevels as $st)
                                    <option value="{{ $st->kode }}" {{ old('student_level') == $st->kode ? 'selected' : '' }}>{{ $st->deskripsi }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label value="Target Fakultas" />
                            <select name="faculty_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                <option value="">-- Semua Fakultas --</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}">{{ $faculty->name }} ({{ $faculty->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label value="Target Program Studi" />
                            <select name="study_program_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                <option value="">-- Semua Prodi --</option>
                                @foreach($studyPrograms as $program)
                                    <option value="{{ $program->id }}">{{ $program->faculty->code }} - {{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <x-input-label value="Maksimal Perubahan Ukuran per Mahasiswa *" />
                        <input type="number" name="max_changes" required value="{{ old('max_changes', 1) }}"
                            min="0" max="255"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <p class="mt-1 text-xs text-gray-400">0 = tidak bisa edit, 1 = 1x perubahan, 2 = 2x perubahan, dst.</p>
                        <x-input-error :messages="$errors->get('max_changes')" class="mt-1" />
                    </div>

                    <div class="pt-4 flex items-center gap-3">
                        <button type="submit" class="bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-5 py-2.5 text-sm font-medium transition">
                            Simpan Event
                        </button>
                        <a href="{{ route('distribution.size-events.index') }}" class="border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg px-4 py-2.5 text-sm font-medium transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
