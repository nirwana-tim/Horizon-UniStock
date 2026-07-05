<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Ukuran Item') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('master-data.item-size.update', $itemSize) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="code" :value="__('Kode')" />
                            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $itemSize->code)" required maxlength="10" />
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="label" :value="__('Label')" />
                            <x-text-input id="label" name="label" type="text" class="mt-1 block w-full" :value="old('label', $itemSize->label)" required />
                            <x-input-error :messages="$errors->get('label')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label :value="__('Kategori')" />
                            <div class="mt-1 grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach($categories as $cat)
                                    <label class="inline-flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-md cursor-pointer hover:bg-gray-100">
                                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                            {{ in_array($cat->id, old('categories', $itemSize->categories->pluck('id')->toArray())) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-gray-800 focus:ring-primary-500">
                                        <span class="text-sm text-gray-700">{{ $cat->label }} ({{ $cat->code }})</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('categories')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-2 mt-6">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            <a href="{{ route('master-data.item-size.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
