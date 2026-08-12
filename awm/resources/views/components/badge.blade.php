@props(['variant' => 'gray'])

@php
    $variants = [
        'green'  => 'bg-green-50 text-green-700 ring-green-600/20',
        'red'    => 'bg-red-50 text-red-700 ring-red-600/20',
        'amber'  => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'blue'   => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'gray'   => 'bg-gray-50 text-gray-700 ring-gray-600/20',
    ];
@endphp

<span {!! $attributes->merge([
    'class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset ' . ($variants[$variant] ?? $variants['gray'])
]) !!} >
    {{ $slot }}
</span>