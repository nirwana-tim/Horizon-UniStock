<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Entitlement') }}</h2>
            <a href="{{ route('master.entitlement.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('← Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Program Studi') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $entitlement->studyProgram?->name ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Level / Angkatan') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $entitlement->programLevel?->name ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Tipe Mahasiswa') }}</dt>
                                <dd class="mt-1"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $entitlement->student_type === 'freshman' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">{{ $entitlement->student_type }}</span></dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Semester') }}</dt>
                                <dd class="mt-1"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $entitlement->semester === 'ganjil' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">{{ ucfirst($entitlement->semester) }}</span></dd>
                            </div>
                            @if($entitlement->description)
                            <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Deskripsi') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $entitlement->description }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <div class="mb-6 flex items-center gap-3">
                        <a href="{{ route('master.entitlement.edit', $entitlement) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Edit') }}</a>
                        <form action="{{ route('master.entitlement.destroy', $entitlement) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus entitlement ini?')">@csrf @method('DELETE')<x-danger-button>{{ __('Hapus') }}</x-danger-button></form>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Daftar Item') }}</h3>
                        @if($entitlement->items->count())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Item</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($entitlement->items as $ei)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ei->item?->name ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ei->item?->code ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ $ei->quantity }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">{{ __('Belum ada item.') }}</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
