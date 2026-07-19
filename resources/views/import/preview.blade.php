<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <x-page-header title="Preview Import" />
                <a href="{{ route('import.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                    {{ __('← Upload Different File') }}
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $importType)) }}</span> &mdash;
                            <strong>{{ $totalDataRows }}</strong> data rows found.
                        </div>
                    </div>

                    @if($rows->isEmpty())
                        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <span>No data rows found in the file. Make sure your file has content.</span>
                        </div>
                    @else
                        <div class="overflow-x-auto mb-6 border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                        @foreach($headers as $header)
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $header }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($rows as $rowIndex => $row)
                                        <tr class="hover:bg-gray-50 {{ $rowIndex >= 5 ? 'hidden' : '' }}">
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                {{ $rowIndex + 1 }}
                                            </td>
                                            @foreach($headers as $header)
                                                <td class="px-4 py-2 whitespace-nowrap text-sm {{ isset($row[$header]) ? 'text-gray-900' : 'text-gray-400' }}">
                                                    {{ $row[$header] ?? '-' }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($totalDataRows > 5)
                                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-sm text-gray-500 text-center">
                                    Showing 5 of {{ $totalDataRows }} rows.
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('import.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="import_type" value="{{ $importType }}">
                            <input type="hidden" name="file_path" value="{{ $filePath }}">

                            <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
                                <a href="{{ route('import.index') }}"
                                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Cancel') }}
                                </a>

                                <x-primary-button>
                                    {{ __('Confirm Import') }}
                                </x-primary-button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
