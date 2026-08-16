<div>
    <x-page-header title="Stock In" subtitle="Receive new stock into inventory">
        <x-button wire:click="openModal">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Stock In
        </x-button>
    </x-page-header>

    <x-data-table empty-text="No stock-in records found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Product</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Lot</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Rack</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Quantity</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Notes</th>
        </x-slot:header>

        @forelse ($items as $movement)
            <tr wire:key="si-{{ $movement->id }}">
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $movement->created_at->format('d M Y H:i') }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $movement->lot->product->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $movement->lot->lot_number ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $movement->rack->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-green-600">+{{ $movement->quantity }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $movement->notes ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">No stock-in records found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $items->links() }}</div>

    <x-modal name="stock-in-modal" wire:model.live="showModal">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Stock In</h2>
        <form wire:submit="save" class="space-y-4">
            <x-select label="Stock Lot" wire:model="selectedLotId" name="selectedLotId" :options="$this->getLotOptions()" placeholder="Select lot..." required :error="$errors->first('selectedLotId')" />
            <x-select label="Rack" wire:model="selectedRackId" name="selectedRackId" :options="$this->getRackOptions()" placeholder="Select rack..." required :error="$errors->first('selectedRackId')" />
            <x-input label="Quantity" wire:model="quantity" name="quantity" type="number" min="1" required :error="$errors->first('quantity')" />
            <x-input label="Notes" wire:model="notes" name="notes" placeholder="Optional notes" :error="$errors->first('notes')" />
            <div class="flex justify-end gap-3 pt-4">
                <x-button variant="secondary" wire:click="closeModal">Cancel</x-button>
                <x-button variant="primary" type="submit">Confirm Stock In</x-button>
            </div>
        </form>
    </x-modal>
</div>