<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Departemen Item') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('master.item-department.update', $itemDepartment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="code" :value="__('Kode')" />
                            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $itemDepartment->code)" required maxlength="2" />
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="label" :value="__('Label')" />
                            <x-text-input id="label" name="label" type="text" class="mt-1 block w-full" :value="old('label', $itemDepartment->label)" required />
                            <x-input-error :messages="$errors->get('label')" class="mt-2" />
                        </div>

                        @php $selectedIds = old('study_program_ids', $itemDepartment->studyPrograms->pluck('id')->toArray()) @endphp

                        <div class="mb-4">
                            <x-input-label :value="__('Program Studi Terkait')" />
                            <div class="mt-2 space-y-4">
                                @foreach($faculties as $faculty)
                                    @php
                                        $facultyProdiIds = $faculty->studyPrograms->pluck('id')->toArray();
                                        $checkedInFaculty = array_intersect($selectedIds, $facultyProdiIds);
                                        $allChecked = count($checkedInFaculty) === count($facultyProdiIds) && count($facultyProdiIds) > 0;
                                        $anyChecked = count($checkedInFaculty) > 0;
                                    @endphp
                                    <div class="border border-gray-200 rounded-md p-3">
                                        <div class="flex items-center mb-2">
                                            <input type="checkbox" id="faculty-{{ $faculty->id }}" class="faculty-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-faculty="{{ $faculty->id }}" {{ $allChecked ? 'checked' : '' }}>
                                            <label for="faculty-{{ $faculty->id }}" class="ml-2 text-sm font-semibold text-gray-700">{{ $faculty->name }}</label>
                                        </div>
                                        <div class="ml-6 space-y-1">
                                            @foreach($faculty->studyPrograms as $prodi)
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="study_program_ids[]" value="{{ $prodi->id }}" class="prodi-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-faculty="{{ $faculty->id }}" {{ in_array($prodi->id, $selectedIds) ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm text-gray-600">{{ $prodi->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('study_program_ids')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-2 mt-6">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            <a href="{{ route('master.item-department.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.faculty-check').forEach(function (facultyCb) {
                var facultyId = facultyCb.dataset.faculty;
                var allProdi = document.querySelectorAll('.prodi-check[data-faculty="' + facultyId + '"]');
                var allChecked = Array.from(allProdi).length > 0 && Array.from(allProdi).every(function (cb) { return cb.checked; });
                var anyChecked = Array.from(allProdi).some(function (cb) { return cb.checked; });
                if (anyChecked && !allChecked) {
                    facultyCb.indeterminate = true;
                }

                facultyCb.addEventListener('change', function () {
                    var fid = this.dataset.faculty;
                    document.querySelectorAll('.prodi-check[data-faculty="' + fid + '"]').forEach(function (cb) {
                        cb.checked = facultyCb.checked;
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
