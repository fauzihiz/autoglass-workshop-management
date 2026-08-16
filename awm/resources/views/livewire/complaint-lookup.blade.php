<div>
    <x-page-header title="Complaint Lookup" subtitle="Trace any past job by customer, vehicle, or product details">
    </x-page-header>

    {{-- Search Form --}}
    <div class="rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Search Criteria</h3>
        <form wire:submit="search" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <x-input label="License Plate" wire:model="licensePlate" name="licensePlate" placeholder="e.g. B 1234 ABC" />
                <x-input label="Customer Name" wire:model="customerName" name="customerName" placeholder="e.g. John Doe" />
                <x-select label="Glass Product" wire:model="glassProductId" name="glassProductId" :options="$this->getProductOptions()" placeholder="Select product..." />
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <x-select label="Vehicle Brand" wire:model="brandId" name="brandId" :options="$this->getBrandOptions()" placeholder="All brands..." />
                <x-select label="Vehicle Model" wire:model="modelId" name="modelId" :options="$this->getModelOptions()" placeholder="All models..." />
                <x-input label="Date From" wire:model="dateFrom" name="dateFrom" type="date" />
                <x-input label="Date To" wire:model="dateTo" name="dateTo" type="date" />
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" wire:click="clear" type="button">Clear</x-button>
                <x-button variant="primary" type="submit">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    Search
                </x-button>
            </div>
        </form>
    </div>

    {{-- Results --}}
    @if ($results !== null)
        <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Results ({{ $results->count() }} transactions found)</h3>
            <x-data-table empty-text="No transactions match your search criteria.">
                <x-slot:header>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Vehicle</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Items</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                </x-slot:header>
                @forelse ($results as $transaction)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900"><a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:underline">{{ $transaction->invoice_number }}</a></td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $transaction->created_at->format('d M Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">@if ($transaction->customer)<a href="{{ route('customers.show', $transaction->customer) }}" class="text-blue-600 hover:underline">{{ $transaction->customer->name }}</a>@else —@endif</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">@if ($transaction->vehicle)<a href="{{ route('vehicles.show', $transaction->vehicle) }}" class="text-blue-600 hover:underline">{{ $transaction->vehicle->license_plate }}</a>@else —@endif</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500"><x-badge variant="blue">{{ $transaction->type->label() }}</x-badge></td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">@foreach ($transaction->items as $item)@if ($item->itemable instanceof \App\Models\GlassProduct)<x-badge variant="purple">{{ $item->itemable->name }}</x-badge>@endif@endforeach</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm"><x-badge variant="{{ $transaction->status === \App\Enums\TransactionStatus::Completed ? 'green' : ($transaction->status === \App\Enums\TransactionStatus::Cancelled ? 'red' : 'yellow') }}">{{ $transaction->status->label() }}</x-badge></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-12 text-center text-sm text-gray-500">No transactions match your search criteria.</td></tr>
                @endforelse
            </x-data-table>
        </div>
    @endif
</div>