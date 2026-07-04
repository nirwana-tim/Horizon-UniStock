{{--
  Empty State Component
  Props:
    - $title: string — Judul empty state
    - $description: string (optional) — Deskripsi tambahan
  Slots:
    - $icon (optional) — Custom SVG icon
    - $actions (optional) — Tombol aksi
  Usage:
    <x-empty-state title="Belum Ada Fakultas" description="Tambahkan fakultas pertama Anda.">
        <x-slot name="actions">
            <a href="{{ route('master.faculty.create') }}" class="...">Tambah</a>
        </x-slot>
    </x-empty-state>
--}}
@props(['title' => 'Belum Ada Data', 'description' => null])

<div class="flex flex-col items-center justify-center py-16 text-center px-4">

    {{-- Icon --}}
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
        @isset($icon)
            {{ $icon }}
        @else
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
        @endisset
    </div>

    <h3 class="text-base font-semibold text-gray-700 mb-1">{{ $title }}</h3>

    @if($description)
        <p class="text-sm text-gray-500 max-w-xs mb-4">{{ $description }}</p>
    @endif

    @isset($actions)
        <div class="flex items-center gap-2 mt-2">
            {{ $actions }}
        </div>
    @endisset

</div>
