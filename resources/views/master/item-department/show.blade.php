<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Item Department Details') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Code') }}</h3>
                            <p class="mt-1 text-sm font-mono text-gray-900">{{ $itemDepartment->code }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Label') }}</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $itemDepartment->label }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Related Items</h3>
                            @if($itemDepartment->items->count())
                                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Code</th>
                                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Item Name</th>
                                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit</th>
                                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($itemDepartment->items as $item)
                                                <tr>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-mono text-gray-900">{{ $item->code }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $item->name }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $item->category?->label ?? '-' }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $item->unit }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-right">
                                                        <a href="{{ route('master-data.item.show', $item->code) }}" class="inline-flex items-center justify-center p-1.5 text-primary-600 hover:text-primary-800 hover:bg-primary-50 rounded-lg transition-colors" title="Lihat Detail">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                            </svg>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="mt-1 text-sm text-gray-400 italic">No items linked to this department yet.</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-6 pt-4 border-t border-gray-200">
                        <a href="{{ route('master-data.item-department.edit', $itemDepartment) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('master-data.item-department.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>