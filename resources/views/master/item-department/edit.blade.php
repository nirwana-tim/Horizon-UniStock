<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Item Department') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('master-data.item-department.update', $itemDepartment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label :value="__('Code')" />
                            <p class="mt-1 text-sm font-mono text-gray-900 bg-gray-100 p-2.5 rounded-md border border-gray-200">{{ $itemDepartment->code }}</p>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="label" :value="__('Label')" />
                            <x-text-input id="label" name="label" type="text" class="mt-1 block w-full" :value="old('label', $itemDepartment->label)" required />
                            <x-input-error :messages="$errors->get('label')" class="mt-2" />
                        </div>

                        @php $selectedIds = old('study_program_ids', $itemDepartment->studyPrograms->pluck('id')->toArray()) @endphp

                        <div class="mb-4">
                            <x-input-label :value="__('Related Study Programs')" />
                            <div class="mt-2 space-y-4" x-data="{ loadedFaculties: {} }">
                                @foreach($faculties as $faculty)
                                    <div class="border border-gray-200 rounded-md p-3">
                                        <div class="flex items-center mb-2">
                                            <input type="checkbox" id="faculty-{{ $faculty->id }}" class="faculty-check rounded border-gray-300 text-primary-600 focus:ring-primary-500" data-faculty="{{ $faculty->id }}">
                                            <label for="faculty-{{ $faculty->id }}" @click="if (!loadedFaculties[{{ $faculty->id }}]) { loadedFaculties[{{ $faculty->id }}] = true; $nextTick(() => { axios.get('{{ route('master-data.item-department.study-programs', $faculty) }}?selected_ids={{ implode(',', $selectedIds) }}').then(r => { $refs['prodi-{{ $faculty->id }}'].innerHTML = r.data; }); }); }" class="ml-2 text-sm font-semibold text-gray-700 cursor-pointer">{{ $faculty->name }}</label>
                                        </div>
                                        <div class="ml-6 space-y-1" x-ref="prodi-{{ $faculty->id }}">
                                            <p class="text-xs text-gray-400 italic">Click faculty name to load study programs...</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('study_program_ids')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-2 mt-6">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                            <a href="{{ route('master-data.item-department.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
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
