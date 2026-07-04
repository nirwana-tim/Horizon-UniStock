{{--
  Stat Card Component
  Props:
    - $title: string — Label statistik
    - $value: mixed — Nilai utama
    - $icon: string (optional) — SVG icon HTML string
    - $color: string (optional) — 'primary'|'green'|'amber'|'red'|'blue'
    - $trend: string (optional) — Teks keterangan tambahan
    - $href: string (optional) — Link jika card bisa diklik
--}}
@props([
    'title',
    'value',
    'icon' => null,
    'color' => 'primary',
    'trend' => null,
    'href' => null,
])

@php
$colorMap = [
    'primary' => ['bg' => 'bg-primary-50', 'text' => 'text-primary-700', 'icon_bg' => 'bg-primary-100'],
    'green'   => ['bg' => 'bg-green-50',   'text' => 'text-green-700',   'icon_bg' => 'bg-green-100'],
    'amber'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',   'icon_bg' => 'bg-amber-100'],
    'red'     => ['bg' => 'bg-red-50',     'text' => 'text-red-700',     'icon_bg' => 'bg-red-100'],
    'blue'    => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',    'icon_bg' => 'bg-blue-100'],
];
$c = $colorMap[$color] ?? $colorMap['primary'];
$tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm {{ $href ? 'hover:shadow-md hover:border-gray-300 transition-all duration-150 cursor-pointer' : '' }}">

    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider truncate">{{ $title }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-800">{{ $value }}</p>
            @if($trend)
                <p class="mt-1 text-xs text-gray-400">{{ $trend }}</p>
            @endif
        </div>

        @if($icon)
            <div class="{{ $c['icon_bg'] }} w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                <div class="{{ $c['text'] }} w-5 h-5">
                    {!! $icon !!}
                </div>
            </div>
        @endif
    </div>

</{{ $tag }}>
