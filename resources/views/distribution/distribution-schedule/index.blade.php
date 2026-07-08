<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Distribution Schedules</h3>
                        <a href="{{ route('distribution.distribution-schedule.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:bg-primary-800 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('+ Add Schedule') }}</a>
                    </div>

                    <div x-data="serverTable('{{ route('distribution.distribution-schedule.index') }}')">

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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program Level</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Faculty / Study Program</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody x-html="tableHtml" class="bg-white divide-y divide-gray-200">
                                    @include('distribution.distribution-schedule._table')
                                </tbody>
                            </table>
                            <div x-html="paginationHtml" class="mt-4">
                                @component('components.alpine-pagination', ['paginator' => $schedules])@endcomponent
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
