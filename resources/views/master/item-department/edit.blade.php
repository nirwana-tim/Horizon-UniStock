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
</x-app-layout>