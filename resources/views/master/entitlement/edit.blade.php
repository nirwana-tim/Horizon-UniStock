<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Entitlement') }}</h2>
            <a href="{{ route('master.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('← Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('master.entitlement.update', $entitlement) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="faculty_id" :value="__('Fakultas')" />
                                <select id="faculty_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Fakultas --</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ $entitlement->studyProgram?->faculty_id == $faculty->id ? 'selected' : '' }}>{{ $faculty->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="study_program_id" :value="__('Program Studi')" />
                                <select id="study_program_id" name="study_program_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Prodi --</option>
                                    @foreach($studyPrograms as $prodi)
                                        <option value="{{ $prodi->id }}" data-faculty="{{ $prodi->faculty_id }}" {{ old('study_program_id', $entitlement->study_program_id) == $prodi->id ? 'selected' : '' }}>{{ $prodi->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('study_program_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="program_level_id" :value="__('Level / Angkatan')" />
                                <select id="program_level_id" name="program_level_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Level --</option>
                                    @foreach($programLevels as $level)
                                        <option value="{{ $level->id }}" {{ old('program_level_id', $entitlement->program_level_id) == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('program_level_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="semester" :value="__('Semester')" />
                                <select id="semester" name="semester" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Semester --</option>
                                    <option value="ganjil" {{ old('semester', $entitlement->semester) == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="genap" {{ old('semester', $entitlement->semester) == 'genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                                <x-input-error :messages="$errors->get('semester')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="student_type" :value="__('Tipe Mahasiswa')" />
                                <select id="student_type" name="student_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="freshman" {{ old('student_type', $entitlement->student_type) == 'freshman' ? 'selected' : '' }}>Freshman</option>
                                    <option value="continuing" {{ old('student_type', $entitlement->student_type) == 'continuing' ? 'selected' : '' }}>Continuing</option>
                                </select>
                                <x-input-error :messages="$errors->get('student_type')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Deskripsi')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="2">{{ old('description', $entitlement->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label :value="__('Item & Jumlah')" />
                                <div id="items-container" class="mt-2 space-y-2">
                                    @php $entitlementItems = old('items', $entitlement->items->map(fn($i) => ['item_id' => $i->item_id, 'quantity' => $i->quantity])->toArray()) @endphp
                                    @forelse($entitlementItems as $idx => $ei)
                                        <div class="item-row flex items-center gap-2 p-2 border rounded bg-gray-50">
                                            <select name="items[{{ $idx }}][item_id]" required class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">-- Pilih Item --</option>
                                                @foreach($items as $it)
                                                    <option value="{{ $it->id }}" {{ $ei['item_id'] == $it->id ? 'selected' : '' }}>{{ $it->name }} ({{ $it->code }})</option>
                                                @endforeach
                                            </select>
                                            <x-text-input name="items[{{ $idx }}][quantity]" type="number" class="w-20" min="1" :value="$ei['quantity'] ?? 1" required />
                                            <button type="button" class="remove-item px-2 py-1 text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                                        </div>
                                    @empty
                                        <div class="item-row flex items-center gap-2 p-2 border rounded bg-gray-50">
                                            <select name="items[0][item_id]" required class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">-- Pilih Item --</option>
                                                @foreach($items as $it)
                                                    <option value="{{ $it->id }}">{{ $it->name }} ({{ $it->code }})</option>
                                                @endforeach
                                            </select>
                                            <x-text-input name="items[0][quantity]" type="number" class="w-20" min="1" value="1" required />
                                            <button type="button" class="remove-item px-2 py-1 text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                                        </div>
                                    @endforelse
                                </div>
                                <button type="button" id="add-item" class="mt-2 inline-flex items-center px-3 py-1 bg-indigo-100 border border-transparent rounded-md font-semibold text-xs text-indigo-700 uppercase tracking-widest hover:bg-indigo-200 focus:bg-indigo-200 active:bg-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">+ Tambah Item</button>
                                <x-input-error :messages="$errors->get('items')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>{{ __('Perbarui') }}</x-primary-button>
                            <a href="{{ route('master.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Batal') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const facultySelect = document.getElementById('faculty_id');
        const prodiSelect = document.getElementById('study_program_id');
        const allProdiOptions = Array.from(prodiSelect.querySelectorAll('option[data-faculty]'));

        facultySelect.addEventListener('change', function () {
            const facultyId = this.value;
            const currentVal = prodiSelect.value;

            prodiSelect.innerHTML = '<option value="">-- Pilih Prodi --</option>';

            allProdiOptions.forEach(function (opt) {
                if (!facultyId || opt.dataset.faculty === facultyId) {
                    prodiSelect.appendChild(opt.cloneNode(true));
                }
            });

            if (currentVal && prodiSelect.querySelector(`option[value="${currentVal}"]`)) {
                prodiSelect.value = currentVal;
            }
        });

        // Trigger faculty filter on page load
        if (facultySelect.value) {
            facultySelect.dispatchEvent(new Event('change'));
        }

        let itemIndex = {{ count($entitlementItems) }};

        document.getElementById('add-item').addEventListener('click', function () {
            const container = document.getElementById('items-container');
            const template = container.querySelector('.item-row').cloneNode(true);

            template.querySelectorAll('select, input').forEach(function (el) {
                const name = el.getAttribute('name');
                if (name) {
                    el.setAttribute('name', name.replace(/\d+/, itemIndex));
                }
                if (el.tagName === 'SELECT') {
                    el.selectedIndex = 0;
                } else {
                    el.value = '1';
                }
            });

            container.appendChild(template);
            itemIndex++;
        });

        document.getElementById('items-container').addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-item')) {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    e.target.closest('.item-row').remove();
                }
            }
        });
    });
</script>
@endpush
