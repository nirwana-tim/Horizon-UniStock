{{--
  Badge Component — Status badge reusable
  Props:
    - $type: 'success'|'warning'|'danger'|'info'|'neutral'|'primary'
  Usage:
    <x-badge type="success">Active</x-badge>
    <x-badge type="warning">Pending</x-badge>
--}}
@props(['type' => 'neutral'])

@php
$styles = [
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-amber-100 text-amber-800',
    'danger'  => 'bg-red-100 text-red-800',
    'info'    => 'bg-blue-100 text-blue-800',
    'neutral' => 'bg-gray-100 text-gray-700',
    'primary' => 'bg-primary-100 text-primary-800',
];
$style = $styles[$type] ?? $styles['neutral'];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $style }}">
    {{ $slot }}
</span>
