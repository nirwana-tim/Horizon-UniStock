<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Mahasiswa</h2>
            <div class="flex gap-2">
                <a href="{{ route('master.student.edit', $student) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 transition">
                    Edit
                </a>
                <a href="{{ route('master.student.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">NIM</h3>
                            <p class="mt-1 text-sm font-mono text-gray-900">{{ $student->nim }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Nama</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Email Kampus</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->email_kampus }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Email Pribadi</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->email_pribadi ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Program Studi</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->studyProgram->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Level / Angkatan</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->programLevel->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Tipe</h3>
                            @if($student->student_type === 'freshman')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Freshman</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Continuing</span>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status Akun</h3>
                            @if($student->user_id)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                <span class="text-xs text-gray-500 ml-2">{{ $student->user?->email }}</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Belum Generate</span>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">QR Token</h3>
                            <p class="mt-1 text-sm font-mono text-gray-900">{{ $student->qr_token ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">QR Generated</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->qr_generated_at ? $student->qr_generated_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                    </div>

                    @if($student->distributionTransactions->count())
                        <div class="mt-8 pt-4 border-t border-gray-200">
                            <h3 class="text-sm font-medium text-gray-500 mb-4">Riwayat Distribusi</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jadwal</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($student->distributionTransactions as $tx)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900">{{ $tx->schedule->name ?? '-' }}</td>
                                                <td class="px-4 py-2 text-sm">
                                                    @if($tx->status === 'completed')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Selesai</span>
                                                    @elseif($tx->status === 'partial')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Sebagian</span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Dibatalkan</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $tx->items->pluck('item.name')->implode(', ') }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $tx->pickup_time ? $tx->pickup_time->format('d/m/Y H:i') : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
