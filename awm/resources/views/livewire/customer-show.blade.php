<div>
    <x-page-header title="{{ $customer->name }}" subtitle="Customer since {{ $customer->created_at->format('M Y') }}">
        <x-button tag="a" href="{{ route('customers.index') }}" variant="secondary">
            ← Back to List
        </x-button>
    </x-page-header>

    {{-- Customer Info --}}
    <div class="rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Customer Information</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <p class="text-sm font-medium text-gray-500">Name</p>
                <p class="text-sm text-gray-900">{{ $customer->name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Phone</p>
                <p class="text-sm text-gray-900">{{ $customer->phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Email</p>
                <p class="text-sm text-gray-900">{{ $customer->email ?? '—' }}</p>
            </div>
            <div class="sm:col-span-3">
                <p class="text-sm font-medium text-gray-500">Address</p>
                <p class="text-sm text-gray-900">{{ $customer->address ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Vehicles --}}
    <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Vehicles ({{ $customer->vehicles->count() }})</h3>
        <x-data-table empty-text="No vehicles registered.">
            <x-slot:header>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">License Plate</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Brand</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Model</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Year</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Color</th>
            </x-slot:header>

            @forelse ($customer->vehicles as $vehicle)
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="text-blue-600 hover:underline">{{ $vehicle->license_plate }}</a>
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $vehicle->brand->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $vehicle->model->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $vehicle->year ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $vehicle->color ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">No vehicles registered.</td></tr>
            @endforelse
        </x-data-table>
    </div>

    {{-- Transactions --}}
    <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Transaction History ({{ $customer->transactions->count() }})</h3>
        <x-data-table empty-text="No transactions found.">
            <x-slot:header>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Vehicle</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
            </x-slot:header>

            @forelse ($customer->transactions as $transaction)
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                        <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:underline">{{ $transaction->invoice_number }}</a>
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $transaction->created_at->format('d M Y') }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $transaction->vehicle->license_plate ?? '—' }}</td>
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