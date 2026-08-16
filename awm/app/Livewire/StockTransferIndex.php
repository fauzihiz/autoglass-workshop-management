<?php

namespace App\Livewire;

use App\Models\Rack;
use App\Models\StockBalance;
use App\Models\StockLot;
use App\Models\StockMovement;
use App\Enums\StockMovementType;
use Livewire\Component;
use Livewire\WithPagination;

class StockTransferIndex extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $selectedLotId = null;
    public ?int $sourceRackId = null;
    public ?int $destRackId = null;
    public int $quantity = 1;
    public string $notes = '';

    public int $sourceAvailable = 0;

    protected function rules(): array
    {
        return [
            'selectedLotId' => 'required|exists:stock_lots,id',
            'sourceRackId' => 'required|exists:racks,id',
            'destRackId' => 'required|exists:racks,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ];
    }

    public function updatedSelectedLotId(): void
    {
        $this->sourceRackId = null;
        $this->destRackId = null;
        $this->sourceAvailable = 0;
    }

    public function updatedSourceRackId(): void
    {
        if ($this->selectedLotId && $this->sourceRackId) {
            $balance = StockBalance::where('stock_lot_id', $this->selectedLotId)
                ->where('rack_id', $this->sourceRackId)
                ->first();
            $this->sourceAvailable = $balance ? (int) $balance->quantity : 0;
        }
    }

    public function openModal(): void
    {
        $this->reset(['selectedLotId', 'sourceRackId', 'destRackId', 'quantity', 'notes']);
        $this->quantity = 1;
        $this->sourceAvailable = 0;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        if ($this->sourceRackId === $this->destRackId) {
            $this->addError('destRackId', 'Source and destination rack must be different.');
            return;
        }

        if ($this->quantity > $this->sourceAvailable) {
            $this->addError('quantity', 'Quantity exceeds available stock at source rack.');
            return;
        }

        // Decrement source balance
        StockBalance::where('stock_lot_id', $this->selectedLotId)
            ->where('rack_id', $this->sourceRackId)
            ->decrement('quantity', $this->quantity);

        // Increment destination balance
        $destBalance = StockBalance::firstOrCreate(
            ['stock_lot_id' => $this->selectedLotId, 'rack_id' => $this->destRackId],
            ['quantity' => 0]
        );
        $destBalance->increment('quantity', $this->quantity);

        // Create movement records
        StockMovement::create([
            'stock_lot_id' => $this->selectedLotId,
            'rack_id' => $this->sourceRackId,
            'type' => StockMovementType::Transfer->value,
            'quantity' => $this->quantity,
            'notes' => ($this->notes ? $this->notes . ' — ' : '') . 'Transfer out',
        ]);

        StockMovement::create([
            'stock_lot_id' => $this->selectedLotId,
            'rack_id' => $this->destRackId,
            'type' => StockMovementType::Transfer->value,
            'quantity' => $this->quantity,
            'notes' => ($this->notes ? $this->notes . ' — ' : '') . 'Transfer in',
        ]);

        $this->closeModal();
        $this->dispatch('saved');
    }

    public function getLotOptions(): array
    {
        return StockLot::with('product')
            ->get()
            ->mapWithKeys(fn ($lot) => [$lot->id => $lot->lot_number . ' — ' . $lot->product->name])
            ->toArray();
    }

    public function getSourceRackOptions(): array
    {
        if (! $this->selectedLotId) {
            return Rack::orderBy('name')->pluck('name', 'id')->toArray();
        }

        return Rack::whereHas('balances', function ($q) {
            $q->where('stock_lot_id', $this->selectedLotId)->where('quantity', '>', 0);
        })->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getDestRackOptions(): array
    {
        return Rack::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getItems()
    {
        return StockMovement::with(['lot.product', 'rack'])
            ->where('type', StockMovementType::Transfer->value)
            ->latest()
            ->paginate($this->perPage ?? 15);
    }

    public function render()
    {
        return view('livewire.stock-transfer-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Stock Transfers',
            'header' => 'Stock Transfers',
        ]);
    }
}