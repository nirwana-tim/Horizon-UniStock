<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Stock Balance</h3>
                        <a href="{{ route('report.stock') }}"
                            class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-700 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-primary-50 transition">
                            Export Excel
                        </a>
                    </div>

                    <div x-data="serverTable('{{ route('inventory.stock-balance.index') }}')">

                        <div class="mb-4 space-y-3">
                            <div>
                                <input type="text"
                                       x-model="search"
                                       @input.debounce.300ms="page=1; fetchData()"
                                       placeholder="Search item name or code..."
                                       class="w-72 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            </div>
                            <div class="flex items-center gap-3">
                                <select x-model="category" @change="page=1; fetchData()"
                                    class="w-48 border-gray-300 rounded-md shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->label }}</option>
                                    @endforeach
                                </select>
                                <select x-model="gender" @change="page=1; fetchData()"
                                    class="w-32 border-gray-300 rounded-md shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">All Genders</option>
                                    <option value="L">Male (L)</option>
                                    <option value="P">Female (P)</option>
                                    <option value="U">Unisex (U)</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Last HPP</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody x-html="tableHtml" class="bg-white divide-y divide-gray-200">
                                    @include('inventory.stock-balance._table')
                                </tbody>
                            </table>
                            <div x-html="paginationHtml" class="mt-4">
                                @component('components.alpine-pagination', ['paginator' => $balances])@endcomponent
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
