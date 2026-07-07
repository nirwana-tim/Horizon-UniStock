<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Vendor Details') }}</h2>
            <a href="{{ route('master-data.vendor.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Vendor Name') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $vendor->name }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $vendor->email ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Contact Person') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $vendor->contact ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Phone Number') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $vendor->phone ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mb-6 flex items-center gap-3">
                        <a href="{{ route('master-data.vendor.edit', $vendor) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit') }}
                        </a>
                        <x-delete-modal
                            :route="route('master-data.vendor.destroy', $vendor)"
                            label="Delete Vendor"
                            description="Are you sure you want to delete vendor {{ $vendor->name }}? This action cannot be undone."
                        />
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Stock Receive History') }}</h3>

                        @if($vendor->stockReceives->count())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($vendor->stockReceives as $receive)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $receive->reference_number }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $receive->receive_date->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $receive->status === 'received' ? 'bg-green-100 text-green-800' : ($receive->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                        {{ $receive->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">{{ __('No stock receipts yet.') }}</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
