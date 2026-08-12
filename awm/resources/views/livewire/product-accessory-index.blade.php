<div>
    <x-page-header title="Product Accessories" subtitle="Manage glass product to accessory associations">
        <x-button wire:click="openCreateModal">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Association
        </x-button>
    </x-page-header>

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search products..." class="block w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    <x-data-table empty-text="No product-accessory associations found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Glass Product</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Associated Accessories</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
        </x-slot:header>

        @forelse ($items as $product)
            <tr wire:key="pa-{{ $product->id }}">
                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $product->name }}</td>
                <td class="px-4 py-3 text-sm text-gray-500">
                    <div class="flex flex-wrap gap-1">
                        @forelse ($product->accessories as $acc)
                            <x-badge variant="blue">{{ $acc->name }}</x-badge>
                        @empty
                            <span class="text-gray-400">None</span>
                        @endforelse
                    </div>
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <x-button variant="secondary" size="sm" wire:click="openAccessoryModal({{ $product->id }})">Add Accessory</x-button>
                </td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-4 py-12 text-center text-sm text-gray-500">No product-accessory associations found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- Add Accessory Modal --}}
    <x-modal name="accessory-assign-modal" wire:model.live="showAccessoryModal">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Add Accessory to Product</h2>
        <form wire:submit="saveAccessory" class="space-y-4">
            <x-select label="Accessory" wire:model="selectedAccessoryId" name="selectedAccessoryId" :options="$this->getAccessoryOptions()" placeholder="Select accessory..." required :error="$errors->first('selectedAccessoryId')" />
            <div class="flex justify-end gap-3 pt-4">
                <x-button variant="secondary" wire:click="$set('showAccessoryModal', false)">Cancel</x-button>
                <x-button variant="primary" type="submit">Add</x-button>
            </div>
        </form>
    </x-modal>
</div>