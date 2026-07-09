<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Import Result') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">{{ __('File Name') }}</h3>
                            <p class="text-sm text-gray-900">{{ $batch->file_name }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">{{ __('Status') }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $batch->status === 'completed' ? 'bg-green-100 text-green-800' : ($batch->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($batch->status) }}
                            </span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">{{ __('Total Rows') }}</h3>
                            <p class="text-sm text-gray-900">{{ $batch->total_rows }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">{{ __('Success / Failed') }}</h3>
                            <p class="text-sm text-gray-900">
                                <span class="text-green-600 font-semibold">{{ $batch->success_rows }}</span> /
                                <span class="text-red-600 font-semibold">{{ $batch->failed_rows }}</span>
                            </p>
                        </div>
                    </div>

                    @php
                        $errors = is_array($batch->error_log)
                            ? $batch->error_log
                            : json_decode($batch->error_log ?? '[]', true);
                    @endphp

                    @if($errors && count($errors) > 0)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">{{ __('Error Log') }}</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Row</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($errors as $index => $error)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $error['row'] ?? $index + 1 }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-red-600">
                                                    @if(isset($error['message']))
                                                        {{ $error['message'] }}
                                                    @elseif(isset($error['errors']) && is_array($error['errors']))
                                                        {{ implode(' ', $error['errors']) }}
                                                    @else
                                                        {{ is_array($error) ? json_encode($error) : $error }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('import.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Back') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
