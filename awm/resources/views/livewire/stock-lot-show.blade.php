<div>
    <x-page-header title="{{ $stockLot->lot_number }}" subtitle="{{ $stockLot->product->name ?? '' }}">
        <x-button tag="a" href="{{ route('inventory.stock-lots.index') }}" variant="secondary">&larr; Back to List</x-button>
    </x-page-header>

    {{-- Lot Info --}}
    <div class="rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Lot Information</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div><p class="text-sm font-medium text-gray-500">Lot Number</p><p class="text-sm font-semibold text-gray-900">{{ $stockLot->lot_number }}</p></div>
            <div>
                <p class="text-sm font-medium text-gray-500">Product</p>
                <p class="text-sm text-gray-900">@if ($stockLot->product)<a href="{{ route('glass-products.show', $stockLot->product) }}" class="text-blue-600 hover:underline">{{ $stockLot->product->name }}</a>@else —@endif</p>
            </div>
            <div><p class="text-sm font-medium text-gray-500">Supplier</p><p class="text-sm text-gray-900">{{ $stockLot->supplier->name ?? '—' }}</p></div>
            <div><p class="text-sm font-medium text-gray-500">Purchase Cost</p><p class="text-sm text-gray-900">Rp {{ number_format($stockLot->purchase_cost, 0, ',', '.') }}</p></div>
            <div><p class="text-sm font-medium text-gray-500">Purchase Date</p><p class="text-sm text-gray-900">{{ $stockLot->purchase_date->format('d M Y') }}</p></div>
            <div><p class="text-sm font-medium text-gray-500">Total Quantity</p><p class="text-sm font-semibold text-gray-900">{{ $stockLot->total_quantity }}</p></div>
            @if ($stockLot->notes)<div class="sm:col-span-3"><p class="text-sm font-medium text-gray-500">Notes</p><p class="text-sm text-gray-900">{{ $stockLot->notes }}</p></div>@endif
        </div>
    </div>

    {{-- Current Balances --}}
    <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Current Balances</h3>
        @if ($stockLot->balances->isEmpty())
            <p class="text-sm text-gray-500">No balance records.</p>
        @else
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                @foreach ($stockLot->balances as $balance)
                    <div class="rounded-lg border border-gray-200 p-3">
                        <p class="text-sm font-medium text-gray-500">{{ $balance->rack->name ?? 'Unknown Rack' }}</p>
                        <p class="text-lg font-bold text-gray-900">{{ $balance->quantity }} units</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
    {{-- Stock Movements --}}
    <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Stock Movements ({{ $stockLot->movements->count() }})</h3>
        <x-data-table empty-text="No stock movements.">
            <x-slot:header>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Rack</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Quantity</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Reference</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Notes</th>
            </x-slot:header>
            @forelse ($stockLot->movements as $movement)
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $movement->created_at->format('d M Y H:i') }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm"><x-badge variant="{{ $movement->type->value === 'in' ? 'green' : ($movement->type->value === 'out' ? 'red' : 'blue') }}">{{ $movement->type->label() }}</x-badge></td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $movement->rack->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold {{ $movement->type->value === 'in' ? 'text-green-600' : 'text-red-600' }}">{{ $movement->type->value === 'in' ? '+' : '-' }}{{ $movement->quantity }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $movement->reference ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $movement->notes ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">No stock movements.</td></tr>
            @endforelse
        </x-data-table>
    </div>

    {{-- Transaction Allocations --}}
    <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Transaction Allocations ({{ $stockLot->allocations->count() }})</h3>
        <x-data-table empty-text="No transaction allocations.">
            <x-slot:header>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Product</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Rack</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Quantity</th>
            </x-slot:header>
            @forelse ($stockLot->allocations as $allocation)
                @php $item = $allocation->transactionItem; $tx = $item->transaction ?? null; @endphp
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $tx?->created_at?->format('d M Y') ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">@if ($tx)<a href="{{ route('transactions.show', $tx) }}" class="text-blue-600 hover:underline">{{ $tx->invoice_number }}</a>@else —@endif</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $item->itemable->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $allocation->rack->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-gray-900">{{ $allocation->quantity }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">No transaction allocations.</td></tr>
            @endforelse
        </x-data-table>
    </div>
</div>