<div>
    <x-page-header title="Car Brands" subtitle="Manage car manufacturer brands">
        <x-button wire:click="openCreateModal">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Brand
        </x-button>
    </x-page-header>

    {{-- Search --}}
    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search brands..." class="block w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    {{-- Table --}}
    <x-data-table empty-text="No car brands found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Slug</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
        </x-slot:header>

        @forelse ($items as $brand)
            <tr wire:key="brand-{{ $brand->id }}">
                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $brand->name }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $brand->slug }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <div class="flex items-center gap-2">
                        <x-button variant="secondary" size="sm" wire:click="openEditModal({{ $brand->id }})">Edit</x-button>
                        <x-button variant="danger" size="sm" wire:click="confirmDelete({{ $brand->id }})">Delete</x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-4 py-12 text-center text-sm text-gray-500">No car brands found.</td>
            </tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">
        {{ $items->links() }}
    </div>

    {{-- Create / Edit Modal --}}
    <x-modal name="car-brand-modal" wire:model.live="showModal">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ $editingId ? 'Edit' : 'Create' }} Car Brand</h2>
        <form wire:submit="save" class="space-y-4">
            <x-input label="Brand Name" wire:model="name" name="name" placeholder="e.g. Toyota" required :error="$errors->first('name')" />
            <x-input label="Slug" wire:model="slug" name="slug" placeholder="e.g. toyota" required :error="$errors->first('slug')" />
            <div class="flex justify-end gap-3 pt-4">
                <x-button variant="secondary" wire:click="closeModal">Cancel</x-button>
                <x-button variant="primary" type="submit">{{ $editingId ? 'Update' : 'Create' }}</x-button>
            </div>
        </form>
    </x-modal>

    {{-- Delete Confirmation --}}
    <x-confirm-delete on-confirm="confirmDelete" />
</div>