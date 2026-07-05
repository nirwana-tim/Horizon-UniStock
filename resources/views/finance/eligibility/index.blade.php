<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Kelayakan Mahasiswa (Eligibility)') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alerts --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="p-6 text-gray-900">
                    
                    {{-- Search Form --}}
                    <div class="mb-6">
                        <form action="{{ route('finance.eligibility.index') }}" method="GET" class="flex gap-2">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari Nama atau NIM Mahasiswa..." class="flex-1 rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm">
                            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition">
                                Cari
                            </button>
                            @if($search)
                                <a href="{{ route('finance.eligibility.index') }}" class="px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition flex items-center">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>

                    {{-- Table --}}
                    @if($students->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">Tidak ada data mahasiswa</h3>
                            <p class="mt-1 text-sm text-gray-500">Sesuaikan kata kunci pencarian Anda atau tambahkan mahasiswa baru.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NIM</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Mahasiswa</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Program Studi</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelayakan</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Pembayaran</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi Kelayakan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($students as $student)
                                        @php
                                            $record = $student->eligibilityRecords->first();
                                            $isEligible = $record && $record->is_eligible;
                                            $paymentStatus = $record ? $record->payment_status : 'Unpaid';
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $student->nim }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ $student->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $student->studyProgram?->name ?? '-' }} ({{ $student->studyProgram?->faculty?->code ?? '-' }})
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($isEligible)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                        Layak (Eligible)
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                                        Tidak Layak
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ strtolower($paymentStatus) === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $paymentStatus }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                                <form action="{{ route('finance.eligibility.toggle', $student) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @if($isEligible)
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-300 text-red-700 hover:bg-red-50 rounded-lg text-xs font-medium transition">
                                                            Set Belum Lunas
                                                        </button>
                                                    @else
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-medium transition">
                                                            Set Lunas (Eligible)
                                                        </button>
                                                    @endif
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $students->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
