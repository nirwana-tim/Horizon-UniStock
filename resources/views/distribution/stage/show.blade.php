<x-app-layout>
    <div class="flex items-center justify-between mb-5">
        <x-page-header title="{{ $stage->name }}" />
        <div class="flex items-center gap-2">
            <a href="{{ route('distribution.stages.edit', $stage) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 transition">{{ __('Edit') }}</a>
            <a href="{{ route('distribution.stages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">{{ __('← Back') }}</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-stat-card title="Stage Order" value="Tahap {{ $stage->stage_order }}" color="primary" />
        <x-stat-card title="Schedules" value="{{ $stage->schedules_count ?? 0 }}" color="info" />
        <x-stat-card title="Transactions" value="{{ $stage->transactions_count ?? 0 }}" color="success" />
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-500">Stage Name</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $stage->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Order</dt>
                <dd class="mt-1 text-sm text-gray-900">Tahap {{ $stage->stage_order }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $stage->start_date?->format('d M Y') ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">End Date</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $stage->end_date?->format('d M Y') ?? '-' }}</dd>
            </div>
            @if($stage->notes)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $stage->notes }}</dd>
            </div>
            @endif
        </dl>
    </div>

    @if($stage->schedules->isNotEmpty())
    <div class="mt-6">
        <h3 class="text-base font-semibold text-gray-800 mb-3">Schedules in this Stage</h3>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stage->schedules as $schedule)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $schedule->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $schedule->date?->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $schedule->location }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-badge type="{{ $schedule->is_active ? 'success' : 'neutral' }}">{{ $schedule->is_active ? 'Active' : 'Inactive' }}</x-badge>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</x-app-layout>
