<div>
    <x-page-header title="Stock Opname" subtitle="Physical count and stock adjustment">
        <x-button wire:click="openCreateModal">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            New Opname
        </x-button>
    </x-page-header>

    <x-data-table empty-text="No stock opname records found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Items</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Notes</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
        </x-slot:header>

        @forelse ($items as $opname)
            <tr wire:key="op-{{ $opname->id }}">
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $opname->opname_date->format('d M Y') }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    @if ($opname->status === 'draft')
                        <x-badge variant="amber">Draft</x-badge>
                    @elseif ($opname->status === 'completed')
                        <x-badge variant="green">Completed</x-badge>
                    @else
                        <x-badge variant="red">Cancelled</x-badge>
                    @endif
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $opname->items_count }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $opname->notes ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <div class="flex items-center gap-2">
                        @if ($opname->status === 'draft')
                            <x-button variant="secondary" size="sm" wire:click="openDetail({{ $opname->id }})">Continue</x-button>
                            <x-button variant="danger" size="sm" wire:click="cancelOpname({{ $opname->id }})">Cancel</x-button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">No stock opname records found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- Create Opname Modal --}}
    <x-modal name="create-opname-modal" wire:model.live="showCreateModal">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">New Stock Opname</h2>
        <form wire:submit="createOpname" class="space-y-4">
            <x-input label="Opname Date" wire:model="opnameDate" name="opnameDate" type="date" required :error="$errors->first('opnameDate')" />
            <x-input label="Notes" wire:model="opnameNotes" name="opnameNotes" placeholder="Optional notes" :error="$errors->first('opnameNotes')" />
            <div class="flex justify-end gap-3 pt-4">
                <x-button variant="secondary" wire:click="closeModal">Cancel</x-button>
                <x-button variant="primary" type="submit">Create</x-button>
            </div>
        </form>
    </x-modal>

    {{-- Opname Detail Modal --}}
    <x-modal name="opname-detail-modal" wire:model.live="showDetailModal" max-width="2xl">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Stock Opname Items</h2>

        @if ($errors->has('opname'))
            <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $errors->first('opname') }}</div>
        @endif

        @php
            $opnameItems = $selectedOpnameId ? \App\Models\StockOpnameItem::where('stock_opname_id', $selectedOpnameId)->with(['lot.product', 'rack'])->get() : collect();
        @endphp

        @if ($opnameItems->isNotEmpty())
            <div class="mb-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium uppercase text-gray-500">Lot</th>
                            <th class="px-3 py-2 text-left text-xs font-medium uppercase text-gray-500">Rack</th>
                            <th class="px-3 py-2 text-left text-xs font-medium uppercase text-gray-500">System</th>
                            <th class="px-3 py-2 text-left text-xs font-medium uppercase text-gray-500">Actual</th>
                            <th class="px-3 py-2 text-left text-xs font-medium uppercase text-gray-500">Diff</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($opnameItems as $item)
                            <tr>
                                <td class="whitespace-nowrap px-3 py-2">{{ $item->lot->lot_number ?? '—' }}</td>
                                <td class="whitespace-nowrap px-3 py-2">{{ $item->rack->name ?? '—' }}</td>
                                <td class="whitespace-nowrap px-3 py-2">{{ $item->system_quantity }}</td>
                                <td class="whitespace-nowrap px-3 py-2">{{ $item->actual_quantity }}</td>
                                <td class="whitespace-nowrap px-3 py-2 font-semibold {{ $item->difference > 0 ? 'text-green-600' : ($item->difference < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                    {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-2">
                                    <button wire:click="removeItem({{ $item->id }})" type="button" class="text-xs text-red-600 hover:text-red-800">Remove</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
            <h3 class="mb-3 text-sm font-semibold text-gray-700">Add Item</h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <x-select label="Stock Lot" wire:model="selectedLotId" name="selectedLotId" :options="$this->getLotOptions()" placeholder="Select lot..." :error="$errors->first('selectedLotId')" />
                <x-select label="Rack" wire:model="selectedRackId" name="selectedRackId" :options="$this->getRackOptions()" placeholder="Select rack..." :error="$errors->first('selectedRackId')" />
            </div>
            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">System Qty</label>
                    <p class="block w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm text-gray-500">{{ $systemQuantity }}</p>
                </div>
                <x-input label="Actual Qty" wire:model="actualQuantity" name="actualQuantity" type="number" min="0" :error="$errors->first('actualQuantity')" />
            </div>
            <x-input label="Notes" wire:model="itemNotes" name="itemNotes" placeholder="Optional" class="mt-3" :error="$errors->first('itemNotes')" />
            <div class="mt-3">
                <x-button variant="secondary" wire:click="addItem">Add Item</x-button>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <x-button variant="secondary" wire:click="closeModal">Close</x-button>
            <x-button variant="primary" wire:click="completeOpname">Complete Opname</x-button>
        </div>
    </x-modal>
</div>