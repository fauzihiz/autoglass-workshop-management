<div>
    <x-page-header title="Suppliers" subtitle="Manage glass and parts suppliers">
        <x-button wire:click="openCreateModal">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Supplier
        </x-button>
    </x-page-header>

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search suppliers..." class="block w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    <x-data-table empty-text="No suppliers found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Contact</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Phone</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
        </x-slot:header>

        @forelse ($items as $supplier)
            <tr wire:key="sup-{{ $supplier->id }}">
                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $supplier->name }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $supplier->contact_person ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $supplier->phone ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $supplier->email ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <x-badge :variant="$supplier->is_active ? 'green' : 'red'">{{ $supplier->is_active ? 'Active' : 'Inactive' }}</x-badge>
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <div class="flex items-center gap-2">
                        <x-button variant="secondary" size="sm" wire:click="openEditModal({{ $supplier->id }})">Edit</x-button>
                        <x-button variant="danger" size="sm" wire:click="confirmDelete({{ $supplier->id }})">Delete</x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">No suppliers found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $items->links() }}</div>

    <x-modal name="supplier-modal" wire:model.live="showModal" max-width="lg">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ $editingId ? 'Edit' : 'Create' }} Supplier</h2>
        <form wire:submit="save" class="space-y-4">
            <x-input label="Supplier Name" wire:model="name" name="name" placeholder="e.g. PT Kaca Jaya" required :error="$errors->first('name')" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-input label="Contact Person" wire:model="contactPerson" name="contactPerson" placeholder="e.g. Andi" :error="$errors->first('contactPerson')" />
                <x-input label="Phone" wire:model="phone" name="phone" placeholder="e.g. 08123456789" :error="$errors->first('phone')" />
            </div>
            <x-input label="Email" wire:model="email" name="email" type="email" placeholder="e.g. andi@kacajaya.com" :error="$errors->first('email')" />
            <x-input label="Address" wire:model="address" name="address" placeholder="e.g. Jl. Industri No. 10" :error="$errors->first('address')" />
            <x-input label="Notes" wire:model="notes" name="notes" placeholder="Optional notes" :error="$errors->first('notes')" />
            <div class="flex items-center gap-2">
                <input type="checkbox" wire:model="isActive" id="isActive" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="isActive" class="text-sm text-gray-700">Active</label>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <x-button variant="secondary" wire:click="closeModal">Cancel</x-button>
                <x-button variant="primary" type="submit">{{ $editingId ? 'Update' : 'Create' }}</x-button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete on-confirm="confirmDelete" />
</div>