@props(['title' => 'Delete Record', 'description' => 'Are you sure you want to delete this record? This action cannot be undone.', 'confirmText' => 'Delete', 'onConfirm' => 'confirmDelete'])

<div
    x-data="{ open: false }"
    x-on:confirm-delete.window="open = true; $dispatch('open-modal', 'confirm-delete')"
    x-on:close-modal.window="$event.detail === 'confirm-delete' ? open = false : null"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-modal="true"
>
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity" x-on:click="open = false"></div>
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative max-w-md w-full transform rounded-xl bg-white p-6 shadow-xl transition-all">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button x-on:click="open = false" type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Cancel</button>
                <button x-on:click="$dispatch('{{ $onConfirm }}'); open = false" type="button" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">{{ $confirmText }}</button>
            </div>
        </div>
    </div>
</div>