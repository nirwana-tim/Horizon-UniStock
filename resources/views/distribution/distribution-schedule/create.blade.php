<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Jadwal Distribusi') }}</h2>
            <a href="{{ route('distribution.distribution-schedule.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('â† Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('distribution.distribution-schedule.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nama Jadwal')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="period" :value="__('Periode')" />
                                <x-text-input id="period" name="period" type="text" class="mt-1 block w-full" :value="old('period')" placeholder="contoh: 2025/2026" />
                                <x-input-error :messages="$errors->get('period')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="program_level_id" :value="__('Angkatan')" />
                                @php
                                    $levelOptions = $programLevels->map(fn($l) => [
                                        'value' => $l->id,
                                        'label' => $l->name,
                                        'group' => '',
                                    ])->toArray();
                                @endphp
                                <x-searchable-select name="program_level_id" :options="$levelOptions" :value="old('program_level_id')" placeholder="-- Semua Angkatan --" />
                                <x-input-error :messages="$errors->get('program_level_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="faculty_id" :value="__('Fakultas')" />
                                @php
                                    $facultyOptions = $faculties->map(fn($f) => [
                                        'value' => $f->id,
                                        'label' => $f->name,
                                        'group' => '',
                                    ])->toArray();
                                @endphp
                                <x-searchable-select name="faculty_id" :options="$facultyOptions" :value="old('faculty_id')" placeholder="-- Semua Fakultas --" />
                                <x-input-error :messages="$errors->get('faculty_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="study_program_id" :value="__('Program Studi')" />
                                @php
                                    $prodiOptions = $studyPrograms->map(fn($sp) => [
                                        'value' => $sp->id,
                                        'label' => $sp->name,
                                        'group' => $sp->faculty->code ?? '',
                                    ])->toArray();
                                @endphp
                                <x-searchable-select name="study_program_id" :options="$prodiOptions" :value="old('study_program_id')" placeholder="-- Semua Prodi --" />
                                <x-input-error :messages="$errors->get('study_program_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="is_active" :value="__('Status Aktif')" />
                                <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="date" :value="__('Tanggal')" />
                                <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date')" required />
                                <x-input-error :messages="$errors->get('date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="location" :value="__('Lokasi')" />
                                <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location')" required />
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="session" :value="__('Sesi / Jam')" />
                                <x-text-input id="session" name="session" type="text" class="mt-1 block w-full" :value="old('session')" required placeholder="09:00-12:00" />
                                <x-input-error :messages="$errors->get('session')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="item_ids" :value="__('Item yang Dibagikan')" />
                                <div class="mt-1 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                    @foreach($items as $item)
                                        <label class="flex items-center space-x-2 p-2 border rounded hover:bg-gray-50">
                                            <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" {{ in_array($item->id, old('item_ids', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                                            <span class="text-sm text-gray-700">{{ $item->name }} ({{ $item->code }})</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('item_ids')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            <a href="{{ route('distribution.distribution-schedule.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Batal') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
