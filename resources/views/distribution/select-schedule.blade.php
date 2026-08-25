<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Pilih Jadwal Distribusi') }}</h2>
            <a href="{{ route('distribution.scan.index') }}" class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg text-sm font-medium transition ease-in-out duration-150">
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Informasi Mahasiswa</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Nama</p>
                            <p class="font-medium text-gray-900">{{ $student->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">NIM</p>
                            <p class="font-medium text-gray-900">{{ $student->nim }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Fakultas</p>
                            <p class="font-medium text-gray-900">{{ $student->faculty->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Angkatan</p>
                            <p class="font-medium text-gray-900">{{ $student->generation->label ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Jadwal yang Sudah Lewat</h3>
                    <p class="text-sm text-gray-600 mb-4">Tidak ada jadwal aktif saat ini. Pilih jadwal yang sudah lewat untuk melanjutkan distribusi.</p>

                    @if($expiredSchedules->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500">Tidak ada jadwal yang tersedia.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($expiredSchedules as $schedule)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-500 hover:bg-primary-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-medium text-gray-900">{{ $schedule->name }}</h4>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $schedule->date->format('d M Y') }}
                                                @if($schedule->start_time && $schedule->end_time)
                                                    | {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}
                                                @endif
                                                @if($schedule->location)
                                                    | {{ $schedule->location }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            @if($schedule->student_has_taken)
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    Sudah Diambil
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                    Belum Diambil
                                                </span>
                                            @endif
                                            <a href="{{ route('distribution.scan.student', ['nim' => $student->nim, 'schedule_id' => $schedule->id]) }}"
                                               class="inline-flex items-center px-3 py-1.5 bg-primary-700 text-white text-sm font-medium rounded-md hover:bg-primary-800">
                                                Pilih
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
