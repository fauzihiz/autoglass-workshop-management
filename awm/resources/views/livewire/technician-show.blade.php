<div>
    <x-page-header title="{{ $technician->name }}" subtitle="Service Technician">
        <x-button tag="a" href="{{ route('technicians.index') }}" variant="secondary">
            ← Back to List
        </x-button>
    </x-page-header>

    {{-- Technician Info --}}
    <div class="rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Technician Information</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <p class="text-sm font-medium text-gray-500">Name</p>
                <p class="text-sm text-gray-900">{{ $technician->name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Phone</p>
                <p class="text-sm text-gray-900">{{ $technician->phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Status</p>
                <p class="text-sm">
                    <x-badge :variant="$technician->is_active ? 'green' : 'red'">{{ $technician->is_active ? 'Active' : 'Inactive' }}</x-badge>
                </p>
            </div>
        </div>
    </div>

    {{-- Service Assignment History --}}
    <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Service Assignment History ({{ $assignments->count() }})</h3>
        <x-data-table empty-text="No service assignments found.">
            <x-slot:header>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Vehicle</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Service</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fee</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
            </x-slot:header>

            @forelse ($assignments as $assignment)
                @php
                    $item = $assignment->transactionItem;
                    $tx = $item->transaction ?? null;
                @endphp
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                        @if ($tx)
                            <a href="{{ route('transactions.show', $tx) }}" class="text-blue-600 hover:underline">{{ $tx->invoice_number }}</a>
                        @else
                            —
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $tx?->created_at?->format('d M Y') ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $tx->customer->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $tx->vehicle->license_plate ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">{{ $item->itemable->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                        <x-badge variant="{{ $tx?->status === \App\Enums\TransactionStatus::Completed ? 'green' : ($tx?->status === \App\Enums\TransactionStatus::Cancelled ? 'red' : 'yellow') }}">
                            {{ $tx?->status?->label() ?? '—' }}
                        </x-badge>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">No service assignments found.</td></tr>
            @endforelse
        </x-data-table>
    </div>
</div>