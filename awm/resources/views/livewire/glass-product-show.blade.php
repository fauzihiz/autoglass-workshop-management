<div>
    <x-page-header title="{{ $glassProduct->name }}" subtitle="{{ $glassProduct->sku }}">
        <x-button tag="a" href="{{ route('glass-products.index') }}" variant="secondary">&larr; Back to List</x-button>
    </x-page-header>

    {{-- Product Info --}}
    <div class="rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Product Information</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div><p class="text-sm font-medium text-gray-500">Name</p><p class="text-sm text-gray-900">{{ $glassProduct->name }}</p></div>
            <div><p class="text-sm font-medium text-gray-500">SKU</p><p class="text-sm text-gray-900"><x-badge>{{ $glassProduct->sku }}</x-badge></p></div>
            <div><p class="text-sm font-medium text-gray-500">Position</p><p class="text-sm text-gray-900">{{ $glassProduct->position->name ?? '—' }}</p></div>
            <div><p class="text-sm font-medium text-gray-500">Minimum Stock</p><p class="text-sm text-gray-900">{{ $glassProduct->minimum_stock }}</p></div>
            <div><p class="text-sm font-medium text-gray-500">Status</p><x-badge :variant="$glassProduct->is_active ? 'green' : 'red'">{{ $glassProduct->is_active ? 'Active' : 'Inactive' }}</x-badge></div>
        </div>
    </div>

    {{-- Compatibilities --}}
    <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Vehicle Compatibility</h3>
        <x-data-table empty-text="No compatibility data.">
            <x-slot:header>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Brand</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Model</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Year From</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Year To</th>
            </x-slot:header>
            @forelse ($glassProduct->compatibilities as $compat)
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $compat->brand->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $compat->model->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $compat->year_from ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $compat->year_to ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-12 text-center text-sm text-gray-500">No compatibility data.</td></tr>
            @endforelse
        </x-data-table>
    </div>
    {{-- Stock Lots --}}
    <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Stock Lots ({{ $glassProduct->stockLots->count() }})</h3>
        <x-data-table empty-text="No stock lots.">
            <x-slot:header>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Lot Number</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Supplier</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Purchase Cost</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Qty</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Balances</th>
            </x-slot:header>
            @forelse ($glassProduct->stockLots as $lot)
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900"><a href="{{ route('inventory.stock-lots.show', $lot) }}" class="text-blue-600 hover:underline">{{ $lot->lot_number }}</a></td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $lot->supplier->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">Rp {{ number_format($lot->purchase_cost, 0, ',', '.') }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $lot->purchase_date->format('d M Y') }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-gray-900">{{ $lot->total_quantity }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                        @forelse ($lot->balances as $balance)
                            <x-badge variant="blue">{{ $balance->rack->name ?? '—' }}: {{ $balance->quantity }}</x-badge>
                        @empty <span class="text-gray-400">—</span> @endforelse
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">No stock lots.</td></tr>
            @endforelse
        </x-data-table>
    </div>

    {{-- Transaction Usage --}}
    <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Transaction Usage ({{ $transactionUsage->count() }})</h3>
        <x-data-table empty-text="No transactions used this product.">
            <x-slot:header>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Vehicle</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
            </x-slot:header>
            @forelse ($transactionUsage as $tx)
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900"><a href="{{ route('transactions.show', $tx) }}" class="text-blue-600 hover:underline">{{ $tx->invoice_number }}</a></td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $tx->created_at->format('d M Y') }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $tx->customer->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $tx->vehicle->license_plate ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm"><x-badge variant="{{ $tx->status === \App\Enums\TransactionStatus::Completed ? 'green' : ($tx->status === \App\Enums\TransactionStatus::Cancelled ? 'red' : 'yellow') }}">{{ $tx->status->label() }}</x-badge></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">No transactions used this product.</td></tr>
            @endforelse
        </x-data-table>
    </div>
</div>