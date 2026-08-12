@props(['name' => 'modal', 'maxWidth' => 'md'])

@php
    $maxWidths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
    ];
    $width = $maxWidths[$maxWidth] ?? $maxWidths['md'];
@endphp

<div
    x-data="{ open: @entangle($name) }"
    x-on:open-modal.window="$event.detail === '{{ $name }}' ? open = true : null"
    x-on:close-modal.window="$event.detail === '{{ $name }}' ? open = false : null"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-modal="true"
>
    <div class="flex min-h-full items-center justify-center p-4">
        <!-- Backdrop -->
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500/75 transition-opacity"
            x-on:click="open = false"
        ></div>

        <!-- Modal panel -->
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative {{ $width }} w-full transform rounded-xl bg-white p-6 shadow-xl transition-all"
        >
            {{ $slot }}
        </div>
    </div>
</div>