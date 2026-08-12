<div x-data="{ activeTab: 'compatibilities' }">
    <x-page-header title="Glass Products" subtitle="Manage glass products, compatibility, and accessories">
        <x-button wire:click="openCreateModal">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Product
        </x-button>
    </x-page-header>

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name, SKU..." class="block w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    <x-data-table empty-text="No glass products found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">SKU</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Position</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Min Stock</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
        </x-slot:header>

        @forelse ($items as $product)
            <tr wire:key="gp-{{ $product->id }}">
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500"><x-badge>{{ $product->sku }}</x-badge></td>
                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $product->name }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $product->glassPosition->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $product->minimum_stock }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <x-badge :variant="$product->is_active ? 'green' : 'red'">{{ $product->is_active ? 'Active' : 'Inactive' }}</x-badge>
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <div class="flex items-center gap-2">
                        <x-button variant="secondary" size="sm" wire:click="openEditModal({{ $product->id }})">Edit</x-button>
                        <x-button variant="danger" size="sm" wire:click="confirmDelete({{ $product->id }})">Delete</x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">No glass products found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- Create / Edit Modal --}}
    <x-modal name="glass-product-modal" wire:model.live="showModal" max-width="lg">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ $editingId ? 'Edit' : 'Create' }} Glass Product</h2>
        <form wire:submit="save" class="space-y-4">
            <x-select label="Glass Position" wire:model="glassPositionId" name="glassPositionId" :options="$this->getPositionOptions()" placeholder="Select position..." required :error="$errors->first('glassPositionId')" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-input label="Product Name" wire:model="name" name="name" placeholder="e.g. Toyota Avanza FW 2020" required :error="$errors->first('name')" />
                <x-input label="SKU" wire:model="sku" name="sku" placeholder="e.g. FW-TOY-AVZ-20" required :error="$errors->first('sku')" />
            </div>
            <x-input label="Description" wire:model="description" name="description" placeholder="Optional description" :error="$errors->first('description')" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-input label="Minimum Stock" wire:model="minimumStock" name="minimumStock" type="number" placeholder="0" required :error="$errors->first('minimumStock')" />
                <div class="flex items-center gap-2 pt-6">
                    <input type="checkbox" wire:model="isActive" id="isActive" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="isActive" class="text-sm text-gray-700">Active</label>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <x-button variant="secondary" wire:click="closeModal">Cancel</x-button>
                <x-button variant="primary" type="submit">{{ $editingId ? 'Update' : 'Create' }}</x-button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete on-confirm="confirmDelete" />
</div>