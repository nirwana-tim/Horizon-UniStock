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

                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer">
                        <input type="checkbox" name="allow_reedit" value="1" {{ old('allow_reedit') ? 'checked' : '' }}
                            class="mt-0.5 rounded border-gray-300 text-primary-700 shadow-sm focus:ring-primary-500">
                        <span>
                            <span class="block text-sm font-semibold text-gray-800">Izinkan Re-Edit Ukuran</span>
                            <span class="block text-xs text-gray-500">Jika dicentang, mahasiswa dapat mengubah ukuran ulang sesuai batas "Maksimal Perubahan" di atas. Jika tidak dicentang, ukuran hanya bisa diisi <strong>sekali saja</strong> (re-edit diblokir).</span>
                        </span>
                    </label>

                    <hr class="my-4 border-gray-200">

                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-gray-800">Opsi Ukuran untuk Mahasiswa</h4>
                        <p class="text-xs text-gray-400">Klik ukuran untuk pilih / hapus. Ukuran lain bisa ditambahkan manual.</p>
                    </div>

                    {{-- BAJU --}}
                    <div class="border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="text-sm font-semibold text-gray-800">UKURAN BAJU <span class="font-normal text-gray-400">(huruf)</span></h5>
                            <span class="text-xs text-gray-400">Kategori: {{ implode(', ', config('student-size.baju_category_codes', [])) }}</span>
                        </div>
                        @if($bajuMasterSizes)
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($bajuMasterSizes as $index => $size)
                                    <input type="checkbox" id="baju-chip-{{ $index }}" name="baju_size_options[]" value="{{ $size }}" class="peer sr-only">
                                    <label for="baju-chip-{{ $index }}"
                                           class="cursor-pointer rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:border-primary-500 peer-checked:bg-primary-700 peer-checked:text-white peer-checked:border-primary-700 transition-colors select-none">
                                        {{ $size }}
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-400 mb-3">Belum ada kategori ukuran baju terkonfigurasi (cek config/student-size.php).</p>
                        @endif
                        <input type="text" name="baju_size_options_custom" value="{{ old('baju_size_options_custom') }}"
                            placeholder="Ukuran baju lain (pisahkan koma), mis: XXL Plus, 3XL"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <x-input-error :messages="$errors->get('baju_size_options_custom')" class="mt-1" />
                    </div>

                    {{-- SEPATU --}}
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="text-sm font-semibold text-gray-800">UKURAN SEPATU <span class="font-normal text-gray-400">(angka)</span></h5>
                            <span class="text-xs text-gray-400">Kategori: {{ implode(', ', config('student-size.sepatu_category_codes', [])) }}</span>
                        </div>
                        @if($sepatuMasterSizes)
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($sepatuMasterSizes as $index => $size)
                                    <input type="checkbox" id="sepatu-chip-{{ $index }}" name="sepatu_size_options[]" value="{{ $size }}" class="peer sr-only">
                                    <label for="sepatu-chip-{{ $index }}"
                                           class="cursor-pointer rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:border-primary-500 peer-checked:bg-primary-700 peer-checked:text-white peer-checked:border-primary-700 transition-colors select-none">
                                        {{ $size }}
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-400 mb-3">Belum ada kategori ukuran sepatu terkonfigurasi (cek config/student-size.php).</p>
                        @endif
                        <input type="text" name="sepatu_size_options_custom" value="{{ old('sepatu_size_options_custom') }}"
                            placeholder="Ukuran sepatu lain (pisahkan koma), mis: 46.5, 47"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <x-input-error :messages="$errors->get('sepatu_size_options_custom')" class="mt-1" />
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
