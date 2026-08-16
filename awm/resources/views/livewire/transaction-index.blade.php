<div>
    <x-page-header title="Transactions" subtitle="Manage sales, installations and service transactions">
        <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            New Transaction
        </a>
    </x-page-header>

    {{-- Filters --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search invoice or customer..." class="block w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        <x-select wire:model="statusFilter" name="statusFilter" :options="$this->getStatusOptions()" placeholder="All Statuses" />
        <x-select wire:model="typeFilter" name="typeFilter" :options="$this->getTypeOptions()" placeholder="All Types" />
    </div>

    <x-data-table empty-text="No transactions found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
        </x-slot:header>

        @forelse ($items as $tx)
            <tr wire:key="tx-{{ $tx->id }}">
                <td class="whitespace-nowrap px-4 py-3 text-sm font-mono font-medium text-gray-900">{{ $tx->invoice_number }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $tx->created_at->format('d M Y') }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $tx->customer->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                    <x-badge variant="blue">{{ $tx->type_label }}</x-badge>
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-gray-900">{{ 'Rp ' . number_format($tx->total_amount, 0, ',', '.') }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <x-badge :variant="$tx->status_color">{{ ucfirst($tx->status) }}</x-badge>
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <a href="{{ route('transactions.show', $tx) }}" class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                        View
                    </a>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">No transactions found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $items->links() }}</div>
</div>