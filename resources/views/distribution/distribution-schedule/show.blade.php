<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Distribution Schedule Detail') }}</h2>
            <a href="{{ route('distribution.distribution-schedule.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('â† Back') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Schedule Name') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $distributionSchedule->name }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Period') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $distributionSchedule->period ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Semester') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $distributionSchedule->semester ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Student Type') }}</dt>
                                <dd class="mt-1">
                                    @if($distributionSchedule->student_type)
                                        <x-badge type="primary">{{ ucfirst($distributionSchedule->student_type) }}</x-badge>
                                    @else
                                        <x-badge type="neutral">All Student Types</x-badge>
                                    @endif
                                </dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Program Level') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $distributionSchedule->programLevel?->name ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Faculty / Study Program') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $distributionSchedule->faculty?->name ?? '-' }}{{ $distributionSchedule->studyProgram ? ' / ' . $distributionSchedule->studyProgram->name : '' }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Date') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $distributionSchedule->date->format('d/m/Y') }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Location') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $distributionSchedule->location }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Session') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $distributionSchedule->session }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $distributionSchedule->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $distributionSchedule->is_active ? 'Active' : 'Inactive' }}</span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mb-6 flex items-center gap-3">
                        <a href="{{ route('distribution.distribution-schedule.edit', $distributionSchedule) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Edit') }}</a>
                        <x-delete-modal
                            :route="route('distribution.distribution-schedule.destroy', $distributionSchedule)"
                            label="Delete Distribution Schedule"
                            description="Are you sure you want to delete schedule {{ $distributionSchedule->name }}? This data cannot be restored."
                        />
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Distributed Items') }}</h3>
                        @if($distributionSchedule->items->count())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($distributionSchedule->items as $scheduleItem)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $scheduleItem->item?->name ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $scheduleItem->item?->code ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">{{ __('No items yet.') }}</p>
                        @endif
                    </div>

                    <div class="border-t border-gray-200 pt-6 mt-6"
                         x-data="{
                             tableHtml: '',
                             paginationHtml: '',
                             currentUrl: '{{ route('distribution.distribution-schedule.transactions', $distributionSchedule) }}',
                             loadData(url) {
                                 axios.get(url).then(res => {
                                     this.tableHtml = res.data.html;
                                     this.paginationHtml = res.data.pagination;
                                 });
                             },
                             init() { this.loadData(this.currentUrl); }
                         }">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ __('Transactions') }}</h3>
                        </div>
                        <div x-html="tableHtml"><p class="text-sm text-gray-400 italic">Loading transactions...</p></div>
                        <div x-html="paginationHtml"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
