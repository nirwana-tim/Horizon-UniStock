<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('Stock Opname') }}</h3>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('templates.download', 'stock_opname') }}" class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 rounded-md text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download Template
                            </a>
                            <button type="button" @click="$dispatch('open-modal', 'import-stock-opname')" class="inline-flex items-center gap-1.5 px-4 py-2 border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-md font-semibold text-xs uppercase tracking-widest transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Import Stock Opname
                            </button>
                            <a href="{{ route('inventory.stock-opname.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:bg-primary-800 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('+ Buat Batch Baru') }}</a>
                        </div>
                    </div>

                    <div x-data="serverTable('{{ route('inventory.stock-opname.index') }}')">

                        <div class="mb-4">
                            <input type="text"
                                   x-model="search"
                                   @input.debounce.300ms="page=1; fetchData()"
                                   placeholder="Search..."
                                   class="w-72 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody x-html="tableHtml" class="bg-white divide-y divide-gray-200">
                                    @include('inventory.stock-opname._table')
                                </tbody>
                            </table>
                            <div x-html="paginationHtml" class="mt-4">
                                @component('components.alpine-pagination', ['paginator' => $batches])@endcomponent
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-import-modal
        name="import-stock-opname"
        type="stock_opname"
        template-type="stock_opname"
        title="Import Stock Opname"
        description="Upload file Excel hasil opname stok fisik untuk dicocokkan dengan stok sistem."
    />
</x-app-layout>
