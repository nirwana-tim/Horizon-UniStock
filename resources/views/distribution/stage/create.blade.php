<x-app-layout>
    <div class="flex items-center justify-between mb-5">
        <x-page-header title="New Distribution Stage" />
        <a href="{{ route('distribution.stages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('← Back') }}</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form action="{{ route('distribution.stages.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="name" :value="__('Stage Name')" :required="true" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" placeholder="e.g. Tahap 1" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="stage_order" :value="__('Order')" :required="true" />
                    <x-text-input id="stage_order" name="stage_order" type="number" class="mt-1 block w-full" :value="old('stage_order', 1)" min="0" required />
                    <x-input-error :messages="$errors->get('stage_order')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="start_date" :value="__('Start Date')" />
                    <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date')" />
                    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="end_date" :value="__('End Date')" />
                    <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date')" />
                    <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="notes" :value="__('Notes')" />
                    <textarea id="notes" name="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" rows="3">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <a href="{{ route('distribution.stages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
