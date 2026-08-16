<div>
    <x-page-header title="Vehicles" subtitle="Manage customer vehicles">
        <x-button wire:click="openCreateModal">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Vehicle
        </x-button>
    </x-page-header>

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by plate, color..." class="block w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    <x-data-table empty-text="No vehicles found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">License Plate</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Brand</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Model</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Year</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
        </x-slot:header>

        @forelse ($items as $vehicle)
            <tr wire:key="veh-{{ $vehicle->id }}">
                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="text-blue-600 hover:underline">{{ $vehicle->license_plate }}</a>
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $vehicle->customer->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $vehicle->brand->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $vehicle->model->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $vehicle->year ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <div class="flex items-center gap-2">
                        <x-button variant="secondary" size="sm" wire:click="openEditModal({{ $vehicle->id }})">Edit</x-button>
                        <x-button variant="danger" size="sm" wire:click="confirmDelete({{ $vehicle->id }})">Delete</x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">No vehicles found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $items->links() }}</div>

    <x-modal name="vehicle-modal" wire:model.live="showModal" max-width="lg">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ $editingId ? 'Edit' : 'Create' }} Vehicle</h2>
        <form wire:submit="save" class="space-y-4">
            <x-select label="Customer" wire:model="customerId" name="customerId" :options="$this->getCustomerOptions()" placeholder="Select customer..." required :error="$errors->first('customerId')" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-select label="Brand" wire:model="carBrandId" name="carBrandId" :options="$this->getBrandOptions()" placeholder="Select brand..." required :error="$errors->first('carBrandId')" />
                <x-select label="Model" wire:model="carModelId" name="carModelId" :options="$this->filteredModels" placeholder="Select model..." required :error="$errors->first('carModelId')" />
            </div>
            <x-input label="License Plate" wire:model="licensePlate" name="licensePlate" placeholder="e.g. B 1234 ABC" required :error="$errors->first('licensePlate')" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <x-input label="Year" wire:model="year" name="year" placeholder="e.g. 2023" :error="$errors->first('year')" />
                <x-input label="Color" wire:model="color" name="color" placeholder="e.g. White" :error="$errors->first('color')" />
                <x-input label="Notes" wire:model="notes" name="notes" placeholder="Optional" :error="$errors->first('notes')" />
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <x-button variant="secondary" wire:click="closeModal">Cancel</x-button>
                <x-button variant="primary" type="submit">{{ $editingId ? 'Update' : 'Create' }}</x-button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete on-confirm="confirmDelete" />
</div>