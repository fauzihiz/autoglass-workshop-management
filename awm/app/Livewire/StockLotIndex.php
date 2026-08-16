<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\GlassProduct;
use App\Models\StockLot;
use App\Models\Supplier;
use Livewire\Component;

class StockLotIndex extends Component
{
    use HasCrudOperations;

    public int $glassProductId = 0;
    public int $supplierId = 0;
    public string $lotNumber = '';
    public float $purchaseCost = 0;
    public string $purchaseDate = '';
    public string $notes = '';

    public function getModelClass(): string
    {
        return StockLot::class;
    }

    public function getTitle(): string
    {
        return 'Stock Lot';
    }

    public function getSearchFields(): array
    {
        return ['lot_number', 'notes'];
    }

    public function resetForm(): void
    {
        $this->glassProductId = 0;
        $this->supplierId = 0;
        $this->lotNumber = '';
        $this->purchaseCost = 0;
        $this->purchaseDate = now()->format('Y-m-d');
        $this->notes = '';
    }

    public function fillFromModel(\Illuminate\Database\Eloquent\Model $record): void
    {
        $this->glassProductId = $record->glass_product_id;
        $this->supplierId = $record->supplier_id;
        $this->lotNumber = $record->lot_number;
        $this->purchaseCost = (float) $record->purchase_cost;
        $this->purchaseDate = $record->purchase_date->format('Y-m-d');
        $this->notes = $record->notes ?? '';
    }

    public function save(): void
    {
        $validated = $this->validate([
            'glassProductId' => 'required|exists:glass_products,id',
            'supplierId' => 'required|exists:suppliers,id',
            'lotNumber' => 'required|string|max:255',
            'purchaseCost' => 'required|numeric|min:0',
            'purchaseDate' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $data = [
            'glass_product_id' => $validated['glassProductId'],
            'supplier_id' => $validated['supplierId'],
            'lot_number' => $validated['lotNumber'],
            'purchase_cost' => $validated['purchaseCost'],
            'purchase_date' => $validated['purchaseDate'],
            'notes' => $validated['notes'] ?? null,
        ];

        if ($this->editingId) {
            StockLot::findOrFail($this->editingId)->update($data);
        } else {
            StockLot::create($data);
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function getProductOptions(): array
    {
        return GlassProduct::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getSupplierOptions(): array
    {
        return Supplier::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getItems()
    {
        $query = StockLot::with(['product', 'supplier', 'balances.rack']);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('lot_number', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
            });
        }

        return $query->latest()->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.stock-lot-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Stock Lots',
            'header' => 'Stock Lots',
        ]);
    }
}