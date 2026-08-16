<div>
    <x-page-header title="Payment History" subtitle="All payments across all transactions">
    </x-page-header>

    {{-- Filters --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by invoice # or customer name..." class="block w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        <div class="w-full max-w-xs">
            <x-select wire:model.live="methodFilter" name="methodFilter" :options="$this->getMethodOptions()" />
        </div>
    </div>

    {{-- Payment Table --}}
    <x-data-table empty-text="No payments found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Method</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Reference</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Notes</th>
        </x-slot:header>

        @forelse ($items as $payment)
            @php $tx = $payment->transaction; @endphp
            <tr>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $payment->paid_at?->format('d M Y H:i') ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                    @if ($tx)
                        <a href="{{ route('transactions.show', $tx) }}" class="text-blue-600 hover:underline">{{ $tx->invoice_number }}</a>
                    @else
                        —
                    @endif
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $tx->customer->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                    <x-badge variant="blue">{{ $payment->method->label() }}</x-badge>
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $payment->reference ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $payment->notes ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">No payments found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $items->links() }}</div>
</div>