<?php

namespace App\Livewire;

use App\Models\StockBalance;
use App\Models\StockLot;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Enums\StockMovementType;
use App\Enums\StockOpnameStatus;
use Livewire\Component;
use Livewire\WithPagination;

class StockOpnameIndex extends Component
{
    use WithPagination;

    public bool $showCreateModal = false;
    public bool $showDetailModal = false;
    public ?int $selectedOpnameId = null;

    public string $opnameDate = '';
    public string $opnameNotes = '';

    public ?int $selectedLotId = null;
    public ?int $selectedRackId = null;
    public int $actualQuantity = 0;
    public int $systemQuantity = 0;
    public string $itemNotes = '';

    public function mount(): void
    {
        $this->opnameDate = now()->format('Y-m-d');
    }

    public function openCreateModal(): void
    {
        $this->opnameDate = now()->format('Y-m-d');
        $this->opnameNotes = '';
        $this->showCreateModal = true;
    }

    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->showDetailModal = false;
        $this->selectedOpnameId = null;
        $this->resetValidation();
    }

    public function createOpname(): void
    {
        $validated = $this->validate([
            'opnameDate' => 'required|date',
            'opnameNotes' => 'nullable|string|max:255',
        ]);

        $opname = StockOpname::create([
            'opname_date' => $validated['opnameDate'],
            'notes' => $validated['opnameNotes'] ?? null,
            'status' => StockOpnameStatus::Draft->value,
        ]);

        $this->showCreateModal = false;
        $this->selectedOpnameId = $opname->id;
        $this->showDetailModal = true;
        $this->dispatch('saved');
    }

    public function openDetail(int $opnameId): void
    {
        $opname = StockOpname::findOrFail($opnameId);
        if ($opname->status !== StockOpnameStatus::Draft->value) {
            return;
        }

        $this->selectedOpnameId = $opnameId;
        $this->showDetailModal = true;
        $this->selectedLotId = null;
        $this->selectedRackId = null;
        $this->actualQuantity = 0;
        $this->systemQuantity = 0;
        $this->itemNotes = '';
    }

    public function updatedSelectedLotId(): void
    {
        $this->selectedRackId = null;
        $this->systemQuantity = 0;
    }

    public function updatedSelectedRackId(): void
    {
        if ($this->selectedLotId && $this->selectedRackId) {
            $balance = StockBalance::where('stock_lot_id', $this->selectedLotId)
                ->where('rack_id', $this->selectedRackId)
                ->first();
            $this->systemQuantity = $balance ? (int) $balance->quantity : 0;
            $this->actualQuantity = $this->systemQuantity;
        }
    }

    public function addItem(): void
    {
        $validated = $this->validate([
            'selectedLotId' => 'required|exists:stock_lots,id',
            'selectedRackId' => 'required|exists:racks,id',
            'actualQuantity' => 'required|integer|min:0',
            'itemNotes' => 'nullable|string|max:255',
        ]);

        $existing = StockOpnameItem::where('stock_opname_id', $this->selectedOpnameId)
            ->where('stock_lot_id', $validated['selectedLotId'])
            ->where('rack_id', $validated['selectedRackId'])
            ->first();

        if ($existing) {
            $existing->update([
                'actual_quantity' => $validated['actualQuantity'],
                'difference' => $validated['actualQuantity'] - $existing->system_quantity,
                'notes' => $validated['itemNotes'] ?? $existing->notes,
            ]);
        } else {
            StockOpnameItem::create([
                'stock_opname_id' => $this->selectedOpnameId,
                'stock_lot_id' => $validated['selectedLotId'],
                'rack_id' => $validated['selectedRackId'],
                'system_quantity' => $this->systemQuantity,
                'actual_quantity' => $validated['actualQuantity'],
                'difference' => $validated['actualQuantity'] - $this->systemQuantity,
                'notes' => $validated['itemNotes'] ?? null,
            ]);
        }

        $this->selectedLotId = null;
        $this->selectedRackId = null;
        $this->actualQuantity = 0;
        $this->systemQuantity = 0;
        $this->itemNotes = '';
        $this->resetValidation();
    }

    public function removeItem(int $itemId): void
    {
        StockOpnameItem::findOrFail($itemId)->delete();
    }

    public function completeOpname(): void
    {
        $opname = StockOpname::findOrFail($this->selectedOpnameId);
        $items = $opname->items;

        if ($items->isEmpty()) {
            $this->addError('opname', 'Add at least one item before completing.');
            return;
        }

        foreach ($items as $item) {
            if ($item->difference != 0) {
                $balance = StockBalance::where('stock_lot_id', $item->stock_lot_id)
                    ->where('rack_id', $item->rack_id)
                    ->first();

                if ($balance) {
                    $balance->update(['quantity' => $item->actual_quantity]);
                } elseif ($item->actual_quantity > 0) {
                    StockBalance::create([
                        'stock_lot_id' => $item->stock_lot_id,
                        'rack_id' => $item->rack_id,
                        'quantity' => $item->actual_quantity,
                    ]);
                }

                StockMovement::create([
                    'stock_lot_id' => $item->stock_lot_id,
                    'rack_id' => $item->rack_id,
                    'type' => StockMovementType::Adjustment->value,
                    'quantity' => abs($item->difference),
                    'reference_type' => StockOpname::class,
                    'reference_id' => $opname->id,
                    'notes' => 'Stock opname adjustment',
                ]);
            }
        }

        $opname->update(['status' => StockOpnameStatus::Completed->value]);
        $this->closeModal();
        $this->dispatch('saved');
    }

    public function cancelOpname(int $opnameId): void
    {
        StockOpname::findOrFail($opnameId)->update(['status' => StockOpnameStatus::Cancelled->value]);
        $this->dispatch('saved');
    }

    public function getLotOptions(): array
    {
        return StockLot::with('product')
            ->get()
            ->mapWithKeys(fn ($lot) => [$lot->id => $lot->lot_number . ' — ' . $lot->product->name])
            ->toArray();
    }

    public function getRackOptions(): array
    {
        if (! $this->selectedLotId) {
            return [];
        }

        return StockBalance::where('stock_lot_id', $this->selectedLotId)
            ->where('quantity', '>', 0)
            ->with('rack')
            ->get()
            ->mapWithKeys(fn ($b) => [$b->rack_id => $b->rack->name])
            ->toArray();
    }

    public function getItems()
    {
        return StockOpname::withCount('items')->latest()->paginate($this->perPage ?? 15);
    }

    public function render()
    {
        return view('livewire.stock-opname-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Stock Opname',
            'header' => 'Stock Opname',
        ]);
    }
}
