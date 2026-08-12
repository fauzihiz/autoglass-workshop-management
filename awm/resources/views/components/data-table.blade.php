@props(['emptyText' => 'No records found.'])

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    {{ $header }}
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    @if ($emptyText && $slot->isEmpty())
        <div class="px-6 py-12 text-center">
            <p class="text-sm text-gray-500">{{ $emptyText }}</p>
        </div>
    @endif
</div>