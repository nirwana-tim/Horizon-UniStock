<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Entitlement') }}</h2>
            <a href="{{ route('distribution.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('← Back') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('distribution.entitlement.update', $entitlement) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label :value="__('Entitlement Code (Read-Only)')" />
                                <x-text-input id="code_display" type="text" class="mt-1 block w-full bg-gray-50 text-gray-500 font-mono" :value="$entitlement->code" disabled />
                                <input type="hidden" name="code" value="{{ $entitlement->code }}">
                            </div>
                            <div>
                                <x-input-label for="student_type" :value="__('Student Type')" />
                                <select id="student_type" name="student_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">-- Select Type --</option>
                                    <option value="year_1_sem_1" {{ old('student_type', $entitlement->student_type) == 'year_1_sem_1' ? 'selected' : '' }}>Year 1 Sem 1</option>
                                    <option value="year_1_sem_2" {{ old('student_type', $entitlement->student_type) == 'year_1_sem_2' ? 'selected' : '' }}>Year 1 Sem 2</option>
                                    <option value="year_2_sem_3" {{ old('student_type', $entitlement->student_type) == 'year_2_sem_3' ? 'selected' : '' }}>Year 2 Sem 3</option>
                                    <option value="year_2_sem_4" {{ old('student_type', $entitlement->student_type) == 'year_2_sem_4' ? 'selected' : '' }}>Year 2 Sem 4</option>
                                    <option value="continuing" {{ old('student_type', $entitlement->student_type) == 'continuing' ? 'selected' : '' }}>Continuing</option>
                                </select>
                                <x-input-error :messages="$errors->get('student_type')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="is_active" :value="__('Status')" />
                                <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="1" {{ old('is_active', $entitlement->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $entitlement->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" rows="2">{{ old('description', $entitlement->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            
                            {{-- Simplified Grid of Checked Items --}}
                            <div class="md:col-span-2">
                                <div x-data="{ gridHtml: '' }" x-init="axios.get('{{ route('distribution.entitlement.items-grid') }}?entitlement_id={{ $entitlement->id }}').then(r => { gridHtml = r.data })">
                                    <div x-html="gridHtml"><p class="text-sm text-gray-400 italic">Loading items...</p></div>
                                </div>
                                <x-input-error :messages="$errors->get('items')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>{{ __('Update') }}</x-primary-button>
                            <a href="{{ route('distribution.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
