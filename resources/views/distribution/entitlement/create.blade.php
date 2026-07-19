<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Add Entitlement') }}</h2>
            <a href="{{ route('distribution.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('← Back') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('distribution.entitlement.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="level_select" :value="__('Program Level')" :required="true" />
                                <select id="level_select" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                    <option value="">-- Select Program Level --</option>
                                    @foreach($programLevels as $level)
                                        <option value="{{ $level->code }}">{{ $level->label }} ({{ $level->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="prodi_select" :value="__('Study Program')" />
                                <select id="prodi_select" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                    <option value="">-- Select Study Program --</option>
                                    @foreach($studyPrograms as $prodi)
                                        <option value="{{ $prodi->code }}" data-faculty="{{ $prodi->faculty?->code ?? '' }}">
                                            {{ $prodi->name }} (Faculty: {{ $prodi->faculty?->code ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="code" :value="__('Entitlement Code (Auto)')" :required="true" />
                                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full bg-gray-50 text-gray-500 font-mono" :value="old('code')" placeholder="Auto-generated..." required readonly />
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="student_type" :value="__('Student Type')" :required="true" />
                                <select id="student_type" name="student_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                    <option value="">-- Select Type --</option>
                                    <option value="year_1_sem_1" {{ old('student_type') == 'year_1_sem_1' ? 'selected' : '' }}>Year 1 Sem 1</option>
                                    <option value="year_1_sem_2" {{ old('student_type') == 'year_1_sem_2' ? 'selected' : '' }}>Year 1 Sem 2</option>
                                    <option value="year_2_sem_3" {{ old('student_type') == 'year_2_sem_3' ? 'selected' : '' }}>Year 2 Sem 3</option>
                                    <option value="year_2_sem_4" {{ old('student_type') == 'year_2_sem_4' ? 'selected' : '' }}>Year 2 Sem 4</option>
                                    <option value="continuing" {{ old('student_type') == 'continuing' ? 'selected' : '' }}>Continuing</option>
                                </select>
                                <x-input-error :messages="$errors->get('student_type')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="is_active" :value="__('Status')" />
                                <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" rows="2">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            
                            {{-- Simplified Grid of Checked Items --}}
                            <div class="md:col-span-2">
                                <div x-data="{ gridHtml: '' }" x-init="axios.get('{{ route('distribution.entitlement.items-grid') }}').then(r => { gridHtml = r.data })">
                                    <div x-html="gridHtml"><p class="text-sm text-gray-400 italic">Loading items...</p></div>
                                </div>
                                <x-input-error :messages="$errors->get('items')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                            <a href="{{ route('distribution.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Auto generation of entitlement code based on selections
        const levelSelect = document.getElementById('level_select');
        const prodiSelect = document.getElementById('prodi_select');
        const codeInput = document.getElementById('code');

        function updateCode() {
            const levelCode = levelSelect.value;
            const selectedProdiOpt = prodiSelect.options[prodiSelect.selectedIndex];
            const facultyCode = selectedProdiOpt ? selectedProdiOpt.dataset.faculty : '';
            const prodiCode = prodiSelect.value;

            if (levelCode && facultyCode && prodiCode) {
                codeInput.value = `${levelCode}${facultyCode}${prodiCode}`;
            } else {
                codeInput.value = '';
            }
        }

        levelSelect.addEventListener('change', updateCode);
        prodiSelect.addEventListener('change', updateCode);
    });
</script>
@endpush
</x-app-layout>
