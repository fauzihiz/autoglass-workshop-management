<div>
    <x-page-header title="Stock Lots" subtitle="Manage supplier lots, purchase costs, and balances">
        <x-button wire:click="openCreateModal">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Stock Lot
        </x-button>
    </x-page-header>

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by lot number or notes..." class="block w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    <x-data-table empty-text="No stock lots found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Lot Number</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Product</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Supplier</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Purchase Cost</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Qty</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Balances</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
        </x-slot:header>

        @forelse ($items as $lot)
            <tr wire:key="lot-{{ $lot->id }}">
                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                    <a href="{{ route('inventory.stock-lots.show', $lot) }}" class="text-blue-600 hover:underline">{{ $lot->lot_number }}</a>
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $lot->product->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $lot->supplier->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">Rp {{ number_format($lot->purchase_cost, 0, ',', '.') }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $lot->purchase_date->format('d M Y') }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-gray-900">{{ $lot->total_quantity }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                    @forelse ($lot->balances as $balance)
                        <x-badge variant="blue">{{ $balance->rack->name ?? '—' }}: {{ $balance->quantity }}</x-badge>
                    @empty
                        <span class="text-gray-400">—</span>
                    @endforelse
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <div class="flex items-center gap-2">
                        <x-button variant="secondary" size="sm" wire:click="openEditModal({{ $lot->id }})">Edit</x-button>
                        <x-button variant="danger" size="sm" wire:click="confirmDelete({{ $lot->id }})">Delete</x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-4 py-12 text-center text-sm text-gray-500">No stock lots found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $items->links() }}</div>

    <x-modal name="stock-lot-modal" wire:model.live="showModal" max-width="lg">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ $editingId ? 'Edit' : 'Create' }} Stock Lot</h2>
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-select label="Glass Product" wire:model="glassProductId" name="glassProductId" :options="$this->getProductOptions()" placeholder="Select product..." required :error="$errors->first('glassProductId')" />
                <x-select label="Supplier" wire:model="supplierId" name="supplierId" :options="$this->getSupplierOptions()" placeholder="Select supplier..." required :error="$errors->first('supplierId')" />
            </div>
            <x-input label="Lot Number" wire:model="lotNumber" name="lotNumber" placeholder="e.g. LOT-2026-001" required :error="$errors->first('lotNumber')" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-input label="Purchase Cost (Rp)" wire:model="purchaseCost" name="purchaseCost" type="number" step="0.01" placeholder="0" required :error="$errors->first('purchaseCost')" />
                <x-input label="Purchase Date" wire:model="purchaseDate" name="purchaseDate" type="date" required :error="$errors->first('purchaseDate')" />
            </div>
            <x-input label="Notes" wire:model="notes" name="notes" placeholder="Optional notes" :error="$errors->first('notes')" />
            <div class="flex justify-end gap-3 pt-4">
                <x-button variant="secondary" wire:click="closeModal">Cancel</x-button>
                <x-button variant="primary" type="submit">{{ $editingId ? 'Update' : 'Create' }}</x-button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete on-confirm="confirmDelete" />
</div>