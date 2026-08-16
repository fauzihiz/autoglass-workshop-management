<div>
    <x-page-header title="{{ $vehicle->license_plate }}" subtitle="{{ $vehicle->brand->name ?? '' }} {{ $vehicle->model->name ?? '' }}">
        <x-button tag="a" href="{{ route('vehicles.index') }}" variant="secondary">
            ← Back to List
        </x-button>
    </x-page-header>

    {{-- Vehicle Info --}}
    <div class="rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Vehicle Information</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <p class="text-sm font-medium text-gray-500">License Plate</p>
                <p class="text-sm font-semibold text-gray-900">{{ $vehicle->license_plate }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Customer</p>
                <p class="text-sm text-gray-900">
                    @if ($vehicle->customer)
                        <a href="{{ route('customers.show', $vehicle->customer) }}" class="text-blue-600 hover:underline">{{ $vehicle->customer->name }}</a>
                    @else
                        —
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Brand / Model</p>
                <p class="text-sm text-gray-900">{{ $vehicle->brand->name ?? '—' }} {{ $vehicle->model->name ?? '' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Year</p>
                <p class="text-sm text-gray-900">{{ $vehicle->year ?? '—' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Color</p>
                <p class="text-sm text-gray-900">{{ $vehicle->color ?? '—' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Notes</p>
                <p class="text-sm text-gray-900">{{ $vehicle->notes ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Transactions --}}
    <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Service History ({{ $vehicle->transactions->count() }})</h3>
        <x-data-table empty-text="No transactions found.">
            <x-slot:header>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
            </x-slot:header>

            @forelse ($vehicle->transactions as $transaction)
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                        <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:underline">{{ $transaction->invoice_number }}</a>
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $transaction->created_at->format('d M Y') }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $transaction->customer->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                        <x-badge variant="blue">{{ $transaction->type->label() }}</x-badge>
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                        <x-badge variant="{{ $transaction->status === \App\Enums\TransactionStatus::Completed ? 'green' : ($transaction->status === \App\Enums\TransactionStatus::Cancelled ? 'red' : 'yellow') }}">
                            {{ $transaction->status->label() }}
                        </x-badge>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">No transactions found.</td></tr>
            @endforelse
        </x-data-table>
    </div>
</div>