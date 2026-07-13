@props([
    'title',
    'value',
    'iconPath' => null,
    'color' => 'primary',
    'trend' => null,
    'href' => null,
    'xValue' => null,
    'loading' => false,
])

@php
$colorMap = [
    'primary' => ['bg' => 'bg-primary-50', 'text' => 'text-primary-700', 'icon_bg' => 'bg-primary-100'],
    'green'   => ['bg' => 'bg-green-50',   'text' => 'text-green-700',   'icon_bg' => 'bg-green-100'],
    'amber'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',   'icon_bg' => 'bg-amber-100'],
    'red'     => ['bg' => 'bg-red-50',     'text' => 'text-red-700',     'icon_bg' => 'bg-red-100'],
    'blue'    => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',    'icon_bg' => 'bg-blue-100'],
    'teal'    => ['bg' => 'bg-teal-50',    'text' => 'text-teal-700',    'icon_bg' => 'bg-teal-100'],
];
$c = $colorMap[$color] ?? $colorMap['primary'];
$tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm {{ $href ? 'hover:shadow-md hover:border-gray-300 transition-all duration-150 cursor-pointer' : '' }}">

    @if($loading)
        <div class="animate-pulse">
            <div class="h-3 w-24 bg-gray-200 rounded mb-3"></div>
            <div class="h-8 w-16 bg-gray-200 rounded mb-2"></div>
            @if($trend)
                <div class="h-3 w-32 bg-gray-200 rounded"></div>
            @endif
        </div>
    @else
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider truncate">{{ $title }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-800" @if($xValue) x-text="{{ $xValue }}" @endif>{{ $xValue ? '0' : $value }}</p>
            @if($trend)
                <p class="mt-1 text-xs text-gray-400">{{ $trend }}</p>
            @endif
        </div>

        @if($iconPath)
            <div class="{{ $c['icon_bg'] }} w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                <svg class="{{ $c['text'] }} w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}" />
                </svg>
            </div>
        @endif
    </div>
    @endif

</{{ $tag }}>
