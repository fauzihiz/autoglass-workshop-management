<?php

namespace App\Livewire;

use App\Models\Rack;
use App\Models\StockBalance;
use App\Models\StockLot;
use App\Models\StockMovement;
use App\Enums\StockMovementType;
use Livewire\Component;
use Livewire\WithPagination;

class StockInIndex extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $selectedLotId = null;
    public ?int $selectedRackId = null;
    public int $quantity = 1;
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'selectedLotId' => 'required|exists:stock_lots,id',
            'selectedRackId' => 'required|exists:racks,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ];
    }

    public function openModal(): void
    {
        $this->reset(['selectedLotId', 'selectedRackId', 'quantity', 'notes']);
        $this->quantity = 1;
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

        // Update or create stock balance
        $balance = StockBalance::firstOrCreate(
            ['stock_lot_id' => $this->selectedLotId, 'rack_id' => $this->selectedRackId],
            ['quantity' => 0]
        );
        $balance->increment('quantity', $this->quantity);

        // Create movement record
        StockMovement::create([
            'stock_lot_id' => $this->selectedLotId,
            'rack_id' => $this->selectedRackId,
            'type' => StockMovementType::In->value,
            'quantity' => $this->quantity,
            'notes' => $this->notes ?: null,
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

    public function getRackOptions(): array
    {
        return Rack::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getItems()
    {
        return StockMovement::with(['lot.product', 'rack'])
            ->where('type', StockMovementType::In->value)
            ->latest()
            ->paginate($this->perPage ?? 15);
    }

    public function render()
    {
        return view('livewire.stock-in-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Stock In',
            'header' => 'Stock In',
        ]);
    }
}