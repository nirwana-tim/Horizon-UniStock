<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Preview Import') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4 p-4 bg-blue-100 border border-blue-300 text-blue-800 rounded-md">
                        {{ __('Tipe Import:') }} <strong>{{ ucfirst(str_replace('_', ' ', $importType)) }}</strong> &mdash;
                        {{ __('Ditemukan') }} <strong>{{ $data->count() }}</strong> {{ __('baris data.') }}
                    </div>

                    @if($data->isEmpty())
                        <div class="mb-4 p-4 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-md">
                            {{ __('Tidak ada data yang ditemukan dalam file.') }}
                        </div>
                    @else
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                        @foreach($data->first() as $colIndex => $cell)
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $colIndex === 0 ? __('Kolom') : 'Kolom ' . ($colIndex + 1) }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($data as $rowIndex => $row)
                                        <tr class="{{ $rowIndex === 0 ? 'font-semibold bg-gray-50' : 'hover:bg-gray-50' }}">
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                {{ $rowIndex + 1 }}
                                            </td>
                                            @foreach($row as $cell)
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 {{ $rowIndex === 0 ? 'text-xs uppercase tracking-wider text-gray-500' : '' }}">
                                                    {{ $cell ?? '-' }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <form action="{{ route('import.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="import_type" value="{{ $importType }}">
                        <input type="hidden" name="file_path" value="{{ $filePath }}">

                        <div class="flex items-center gap-4">
                            <a href="{{ route('import.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Kembali') }}
                            </a>

                            @if($data->isNotEmpty())
                                <x-primary-button class="bg-[#980416] hover:bg-[#7a0311]">
                                    {{ __('Konfirmasi Import') }}
                                </x-primary-button>
                            @endif
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
