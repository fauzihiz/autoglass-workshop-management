<div>
    <x-page-header title="Racks" subtitle="Manage storage rack locations">
        <x-button wire:click="openCreateModal">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Rack
        </x-button>
    </x-page-header>

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search racks..." class="block w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    <x-data-table empty-text="No racks found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Location</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
        </x-slot:header>

        @forelse ($items as $rack)
            <tr wire:key="rack-{{ $rack->id }}">
                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $rack->name }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $rack->location_description ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <div class="flex items-center gap-2">
                        <x-button variant="secondary" size="sm" wire:click="openEditModal({{ $rack->id }})">Edit</x-button>
                        <x-button variant="danger" size="sm" wire:click="confirmDelete({{ $rack->id }})">Delete</x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-4 py-12 text-center text-sm text-gray-500">No racks found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $items->links() }}</div>

    <x-modal name="rack-modal" wire:model.live="showModal">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ $editingId ? 'Edit' : 'Create' }} Rack</h2>
        <form wire:submit="save" class="space-y-4">
            <x-input label="Rack Name" wire:model="name" name="name" placeholder="e.g. Rack A1" required :error="$errors->first('name')" />
            <x-input label="Location Description" wire:model="locationDescription" name="locationDescription" placeholder="e.g. Building 1, Row 3" :error="$errors->first('locationDescription')" />
            <div class="flex justify-end gap-3 pt-4">
                <x-button variant="secondary" wire:click="closeModal">Cancel</x-button>
                <x-button variant="primary" type="submit">{{ $editingId ? 'Update' : 'Create' }}</x-button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete on-confirm="confirmDelete" />
</div>