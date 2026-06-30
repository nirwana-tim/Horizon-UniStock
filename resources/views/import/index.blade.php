<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Import Data') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            <div>
                                <x-input-label for="import_type" :value="__('Tipe Import')" />
                                <select name="import_type" id="import_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Pilih Tipe Import --</option>
                                    <option value="students" {{ old('import_type') === 'students' ? 'selected' : '' }}>Students</option>
                                    <option value="eligible" {{ old('import_type') === 'eligible' ? 'selected' : '' }}>Eligible</option>
                                    <option value="items" {{ old('import_type') === 'items' ? 'selected' : '' }}>Items</option>
                                    <option value="stock_opname" {{ old('import_type') === 'stock_opname' ? 'selected' : '' }}>Stock Opname</option>
                                </select>
                                <x-input-error :messages="$errors->get('import_type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="file" :value="__('File Excel')" />
                                <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                <x-input-error :messages="$errors->get('file')" class="mt-2" />
                            </div>

                            <div class="flex items-end">
                                <x-primary-button class="w-full justify-center">
                                    {{ __('Import') }}
                                </x-primary-button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
