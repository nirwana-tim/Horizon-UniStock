<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard Mahasiswa') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Akun</h3>
                        @if(session('email_success'))
                            <span class="text-sm text-green-600 font-semibold">{{ session('email_success') }}</span>
                        @endif
                    </div>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Nama</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $student->name }}</dd>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">NIM</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $student->nim }}</dd>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Program Studi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->studyProgram?->name ?? '-' }}</dd>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Email Kampus</dt>
                            @if($student->email_kampus)
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $student->email_kampus }}
                                    @if($student->email_verified_at)
                                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Terverifikasi</span>
                                    @else
                                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Belum verifikasi</span>
                                    @endif
                                </dd>
                            @else
                                <dd class="mt-1">
                                    <form action="{{ route('student.email.send-otp') }}" method="POST" class="flex gap-2">
                                        @csrf
                                        <input type="email" name="email_kampus" placeholder="nama@krw.horizon.ac.id" required
                                            class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                            Verifikasi
                                        </button>
                                    </form>
                                    <x-input-error :messages="$errors->get('email_kampus')" class="mt-1" />
                                    @if(session('warning'))
                                        <p class="mt-1 text-xs text-yellow-600">{{ session('warning') }}</p>
                                    @endif
                                </dd>
                            @endif
                        </div>
                    </dl>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Status Ukuran</p>
                    @if($hasFilledSize)
                        <p class="mt-1 text-sm font-semibold text-green-600">Sudah diisi</p>
                    @else
                        <p class="mt-1 text-sm font-semibold text-yellow-600">Belum diisi</p>
                        <a href="{{ route('student.sizes.index') }}" class="mt-2 inline-block text-xs text-indigo-600 hover:text-indigo-800">Input ukuran sekarang →</a>
                    @endif
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">QR Identitas</p>
                    @if($hasQr)
                        <p class="mt-1 text-sm font-semibold text-green-600">Tersedia</p>
                        <a href="{{ route('student.qr') }}" class="mt-2 inline-block text-xs text-indigo-600 hover:text-indigo-800">Lihat QR →</a>
                    @else
                        <p class="mt-1 text-sm font-semibold text-yellow-600">Belum digenerate</p>
                        <p class="mt-1 text-xs text-gray-400">Isi ukuran terlebih dahulu</p>
                    @endif
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Status Pengambilan</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $recentTransactions->count() > 0 ? $recentTransactions->first()->status : 'Belum ada transaksi' }}</p>
                </div>
            </div>

            @if($activeSchedules->count())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Distribusi Aktif</h3>
                    <div class="space-y-3">
                        @foreach($activeSchedules as $schedule)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $schedule->name }}</p>
                                <p class="text-xs text-gray-500">{{ $schedule->stage?->name ?? '-' }} | {{ $schedule->location }}</p>
                            </div>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if($recentTransactions->count())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Transaksi</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tahap</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentTransactions as $tx)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $tx->pickup_time?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $tx->stage?->name ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tx->status === 'completed' ? 'bg-green-100 text-green-800' : ($tx->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ $tx->status }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Menu Cepat</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('student.sizes.index') }}" class="px-4 py-3 bg-gray-100 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 text-center">Input Ukuran</a>
                        <a href="{{ route('student.qr') }}" class="px-4 py-3 bg-gray-100 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 text-center">QR Saya</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
