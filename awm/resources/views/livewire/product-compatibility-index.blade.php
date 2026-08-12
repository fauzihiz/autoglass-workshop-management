<div>
    <x-page-header title="Product Compatibilities" subtitle="Manage product-vehicle compatibility mappings">
        <x-button wire:click="openCreateModal">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Compatibility
        </x-button>
    </x-page-header>

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by product name..." class="block w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    <x-data-table empty-text="No compatibility records found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Glass Product</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Car Model</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Year From</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Year To</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
        </x-slot:header>

        @forelse ($items as $compat)
            <tr wire:key="compat-{{ $compat->id }}">
                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $compat->glassProduct->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $compat->carModel->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $compat->year_from ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $compat->year_to ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <div class="flex items-center gap-2">
                        <x-button variant="secondary" size="sm" wire:click="openEditModal({{ $compat->id }})">Edit</x-button>
                        <x-button variant="danger" size="sm" wire:click="confirmDelete({{ $compat->id }})">Delete</x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">No compatibility records found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $items->links() }}</div>

    <x-modal name="compat-modal" wire:model.live="showModal" max-width="lg">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ $editingId ? 'Edit' : 'Create' }} Compatibility</h2>
        <form wire:submit="save" class="space-y-4">
            <x-select label="Glass Product" wire:model="glassProductId" name="glassProductId" :options="$this->getProductOptions()" placeholder="Select product..." required :error="$errors->first('glassProductId')" />
            <x-select label="Car Model" wire:model="carModelId" name="carModelId" :options="$this->getModelOptions()" placeholder="Select model..." required :error="$errors->first('carModelId')" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-input label="Year From" wire:model="yearFrom" name="yearFrom" placeholder="e.g. 2018" :error="$errors->first('yearFrom')" />
                <x-input label="Year To" wire:model="yearTo" name="yearTo" placeholder="e.g. 2023" :error="$errors->first('yearTo')" />
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <x-button variant="secondary" wire:click="closeModal">Cancel</x-button>
                <x-button variant="primary" type="submit">{{ $editingId ? 'Update' : 'Create' }}</x-button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete on-confirm="confirmDelete" />
</div>