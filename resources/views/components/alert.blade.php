{{--
  Alert Component — Unified flash message
  Props:
    - $type: 'success'|'error'|'warning'|'info'
    - $dismissible: bool (default true)
  Usage:
    <x-alert type="success">Data saved successfully</x-alert>
    or auto from session:
    @if(session('success')) <x-alert type="success">{{ session('success') }}</x-alert> @endif
--}}
@props(['type' => 'success', 'dismissible' => true])

@php
$config = [
    'success' => [
        'bg'        => 'bg-green-50 border-green-200',
        'text'      => 'text-green-800',
        'icon_path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'close'     => 'text-green-500 hover:text-green-700',
    ],
    'error' => [
        'bg'        => 'bg-red-50 border-red-200',
        'text'      => 'text-red-800',
        'icon_path' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'close'     => 'text-red-500 hover:text-red-700',
    ],
    'warning' => [
        'bg'        => 'bg-amber-50 border-amber-200',
        'text'      => 'text-amber-800',
        'icon_path' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'close'     => 'text-amber-500 hover:text-amber-700',
    ],
    'info' => [
        'bg'        => 'bg-blue-50 border-blue-200',
        'text'      => 'text-blue-800',
        'icon_path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'close'     => 'text-blue-500 hover:text-blue-700',
    ],
];
$c = $config[$type] ?? $config['info'];
@endphp

<div x-data="{ show: true }" x-show="show"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="flex items-start gap-3 p-4 mb-4 rounded-lg border {{ $c['bg'] }} {{ $c['text'] }}">

    {{-- Icon --}}
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c['icon_path'] }}" />
    </svg>

    {{-- Message --}}
    <div class="flex-1 text-sm">
        {{ $slot }}
    </div>

    {{-- Close button --}}
    @if($dismissible)
    <button @click="show = false"
            class="{{ $c['close'] }} transition-colors ml-1 flex-shrink-0"
            aria-label="Close notification">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    @endif

</div>
