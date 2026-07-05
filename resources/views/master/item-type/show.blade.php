<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Tipe Item') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Kode') }}</h3>
                            <p class="mt-1 text-sm font-mono text-gray-900">{{ $itemType->code }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Label') }}</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $itemType->label }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Kategori Terkait</h3>
                            @if($itemType->categories->count())
                                <div class="mt-1 flex flex-wrap gap-1.5">
                                    @foreach($itemType->categories as $cat)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                            {{ $cat->label }} ({{ $cat->code }})
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-1 text-sm text-gray-400 italic">Belum terhubung ke kategori manapun</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-6 pt-4 border-t border-gray-200">
                        <a href="{{ route('master-data.item-type.edit', $itemType) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('master-data.item-type.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Kembali') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
