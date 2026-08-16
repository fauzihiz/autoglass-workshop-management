<div>
    <x-page-header title="Stock Movements" subtitle="History of all stock in, out, transfer, and adjustment movements">
    </x-page-header>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by lot number, product name, or SKU..." class="block w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        <select wire:model.live="typeFilter" class="block w-full max-w-xs rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            <option value="">All Types</option>
            <option value="in">Stock In</option>
            <option value="out">Stock Out</option>
            <option value="transfer">Transfer</option>
            <option value="adjustment">Adjustment</option>
        </select>
    </div>

    <x-data-table empty-text="No stock movements found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Product</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Lot</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Rack</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Quantity</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Notes</th>
        </x-slot:header>

        @forelse ($items as $movement)
            <tr wire:key="mov-{{ $movement->id }}">
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $movement->created_at->format('d M Y H:i') }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    @if ($movement->type === 'in')
                        <x-badge variant="green">In</x-badge>
                    @elseif ($movement->type === 'out')
                        <x-badge variant="red">Out</x-badge>
                    @elseif ($movement->type === 'transfer')
                        <x-badge variant="blue">Transfer</x-badge>
                    @else
                        <x-badge variant="amber">Adjustment</x-badge>
                    @endif
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $movement->lot->product->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $movement->lot->lot_number ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $movement->rack->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold {{ $movement->type === 'in' ? 'text-green-600' : ($movement->type === 'out' ? 'text-red-600' : 'text-blue-600') }}">
                    @if ($movement->type === 'in') +@endif{{ $movement->quantity }}
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $movement->notes ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">No stock movements found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $items->links() }}</div>
</div>