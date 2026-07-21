<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-page-header title="Event Ganti / Pengisian Ukuran Baju">
                <x-slot name="actions">
                    <a href="{{ route('distribution.size-events.create') }}" class="bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2 text-sm font-medium transition">
                        + Buat Event Baru
                    </a>
                </x-slot>
            </x-page-header>

            @if(session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Event</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Mulai</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline / Akhir</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Target Mahasiswa</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Max Perubahan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($events as $event)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $event->title }}</div>
                                    @if($event->description)
                                        <div class="text-xs text-gray-500">{{ $event->description }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $event->start_date?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $event->end_date?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-700">
                                    <div>Fakultas: <span class="font-semibold">{{ $event->faculty?->name ?? 'Semua' }}</span></div>
                                    <div>Prodi: <span class="font-semibold">{{ $event->studyProgram?->name ?? 'Semua' }}</span></div>
                                    <div>Angkatan: <span class="font-semibold">{{ $event->programLevel?->name ?? 'Semua' }}</span></div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $event->max_changes }}x
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($event->is_active && now()->between($event->start_date, $event->end_date))
                                        <x-badge type="success">Sedang Berlangsung</x-badge>
                                    @elseif(now()->gt($event->end_date))
                                        <x-badge type="neutral">Berakhir</x-badge>
                                    @else
                                        <x-badge type="warning">Belum Mulai</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm">
                                    <form action="{{ route('distribution.size-events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Hapus event ganti ukuran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500 text-sm">
                                    Belum ada Event Ganti Ukuran yang dibuat. Klik tombol "+ Buat Event Baru" di atas untuk membuat event.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
