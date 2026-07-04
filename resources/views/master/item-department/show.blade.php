<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Departemen Item') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Kode') }}</h3>
                            <p class="mt-1 text-sm font-mono text-gray-900">{{ $itemDepartment->code }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Label') }}</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $itemDepartment->label }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Program Studi Terkait') }}</h3>
                            @if($itemDepartment->studyPrograms->count())
                                <div class="mt-2 space-y-1">
                                    @foreach($itemDepartment->studyPrograms->groupBy(fn($sp) => $sp->faculty?->name ?? 'Lainnya') as $facultyName => $programs)
                                        <div>
                                            <span class="text-xs font-semibold text-indigo-700">{{ $facultyName }}</span>
                                            <div class="ml-3 mt-1">
                                                @foreach($programs as $program)
                                                    <span class="inline-block px-2 py-0.5 text-xs bg-gray-100 text-gray-700 rounded-full mr-1 mb-1">{{ $program->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-1 text-sm text-gray-400">Belum ada program studi terkait.</p>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Item') }}</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $itemDepartment->items_count ?? $itemDepartment->items()->count() }} item</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-6 pt-4 border-t border-gray-200">
                        <a href="{{ route('master.item-department.edit', $itemDepartment) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('master.item-department.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
