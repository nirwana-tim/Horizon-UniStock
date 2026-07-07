<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Identity QR Code') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $student->name }}</h3>
                            <p class="text-gray-600">NIM: {{ $student->nim }}</p>
                            <p class="text-sm text-gray-500">{{ $student->studyProgram->name ?? '-' }} | {{ $student->programLevel->name ?? '-' }}</p>
                        </div>

                        <div class="mb-6 inline-block p-4 bg-white border-2 border-gray-200 rounded-lg">
                            <img src="{{ $qrDataUrl }}" alt="QR Code {{ $student->nim }}" class="w-64 h-64">
                        </div>

                        <div class="flex items-center justify-center gap-4">
                            <a href="{{ $qrDataUrl }}"
                               download="qr-{{ $student->nim }}.png"
                               class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:bg-primary-800 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Download QR (PNG)') }}
                            </a>
                            <a href="{{ route('student.sizes.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Back') }}
                            </a>
                        </div>

                        <div class="mt-8 p-4 bg-amber-50 border border-amber-200 rounded-md text-left">
                            <p class="text-sm text-amber-700">
                                <strong>Instructions:</strong>
                            </p>
                            <ul class="mt-2 text-sm text-amber-600 list-disc list-inside space-y-1">
                                <li>Show this QR Code to the officer when picking up uniforms.</li>
                                <li>This QR Code contains your NIM and is permanent.</li>
                                <li>Do not share your QR Code with others.</li>
                            </ul>
                        </div>

                        @if(isset($activeSchedules) && $activeSchedules->count())
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-md text-left">
                            <p class="text-sm text-blue-700 font-semibold mb-3">Active Distribution Schedule</p>
                            <div class="space-y-2">
                                @foreach($activeSchedules as $schedule)
                                <div class="flex items-center justify-between p-2 bg-white rounded border border-blue-100">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $schedule->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $schedule->stage?->name ?? '-' }} | {{ $schedule->location }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
