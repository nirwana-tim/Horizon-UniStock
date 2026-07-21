<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Add Student') }}</h2>
            <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('students.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="nim" :value="__('NIM')" :required="true" />
                                <x-text-input id="nim" name="nim" type="text" class="mt-1 block w-full" :value="old('nim')" required autofocus />
                                <x-input-error :messages="$errors->get('nim')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="name" :value="__('Full Name')" :required="true" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email_kampus" :value="__('Campus Email')" :required="true" />
                                <x-text-input id="email_kampus" name="email_kampus" type="email" class="mt-1 block w-full" :value="old('email_kampus')" placeholder="nim@krw.horizon.ac.id" required />
                                <x-input-error :messages="$errors->get('email_kampus')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email_pribadi" :value="__('Personal Email')" />
                                <x-text-input id="email_pribadi" name="email_pribadi" type="email" class="mt-1 block w-full" :value="old('email_pribadi')" />
                                <x-input-error :messages="$errors->get('email_pribadi')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="study_program_id" :value="__('Study Program')" :required="true" />
                                <select id="study_program_id" name="study_program_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">-- Select Study Program --</option>
                                    @foreach($studyPrograms as $p)
                                        <option value="{{ $p->id }}" {{ old('study_program_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('study_program_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="program_level_id" :value="__('Level / Batch')" :required="true" />
                                <select id="program_level_id" name="program_level_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">-- Select Level --</option>
                                    @foreach($programLevels as $l)
                                        <option value="{{ $l->id }}" {{ old('program_level_id') == $l->id ? 'selected' : '' }}>{{ $l->label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('program_level_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="student_type" :value="__('Student Type')" :required="true" />
                                <select id="student_type" name="student_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    @foreach($studentTypes as $st)
                                        <option value="{{ $st->kode }}" {{ old('student_type') == $st->kode ? 'selected' : '' }}>{{ $st->deskripsi }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('student_type')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                            <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
