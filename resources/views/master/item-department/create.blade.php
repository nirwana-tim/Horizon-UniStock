<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Departemen Item') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('master.item-department.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="code" :value="__('Kode')" />
                            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code')" required maxlength="2" placeholder="Contoh: 02, 04, 06" />
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="label" :value="__('Label')" />
                            <x-text-input id="label" name="label" type="text" class="mt-1 block w-full" :value="old('label')" required placeholder="Contoh: STIKES, STMIK, STIE" />
                            <x-input-error :messages="$errors->get('label')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label :value="__('Program Studi Terkait')" />
                            <div class="mt-2 space-y-4">
                                @foreach($faculties as $faculty)
                                    <div class="border border-gray-200 rounded-md p-3">
                                        <div class="flex items-center mb-2">
                                            <input type="checkbox" id="faculty-{{ $faculty->id }}" class="faculty-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-faculty="{{ $faculty->id }}">
                                            <label for="faculty-{{ $faculty->id }}" class="ml-2 text-sm font-semibold text-gray-700">{{ $faculty->name }}</label>
                                        </div>
                                        <div class="ml-6 space-y-1">
                                            @foreach($faculty->studyPrograms as $prodi)
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="study_program_ids[]" value="{{ $prodi->id }}" class="prodi-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-faculty="{{ $faculty->id }}" {{ in_array($prodi->id, old('study_program_ids', [])) ? 'checked' : '' }}>
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
                facultyCb.addEventListener('change', function () {
                    var facultyId = this.dataset.faculty;
                    document.querySelectorAll('.prodi-check[data-faculty="' + facultyId + '"]').forEach(function (cb) {
                        cb.checked = facultyCb.checked;
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
