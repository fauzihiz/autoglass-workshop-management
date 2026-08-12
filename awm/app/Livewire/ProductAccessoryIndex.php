<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\Accessory;
use App\Models\GlassProduct;

class ProductAccessoryIndex extends \Livewire\Component
{
    use HasCrudOperations;

    public int $glassProductId = 0;
    public int $accessoryId = 0;
    public float $defaultPrice = 0;

    public function getModelClass(): string
    {
        return GlassProduct::class;
    }

    public function getTitle(): string
    {
        return 'Product Accessory';
    }

    public function getSearchFields(): array
    {
        return [];
    }

    public function resetForm(): void
    {
        $this->glassProductId = 0;
        $this->accessoryId = 0;
        $this->defaultPrice = 0;
    }

    public function fillFromModel($record): void
    {
        if (is_array($record)) {
            $this->glassProductId = $record['glass_product_id'] ?? 0;
            $this->accessoryId = $record['accessory_id'] ?? 0;
            $this->defaultPrice = $record['pivot_default_price'] ?? 0;
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'glassProductId' => 'required|exists:glass_products,id',
            'accessoryId' => 'required|exists:accessories,id',
            'defaultPrice' => 'required|numeric|min:0',
        ]);

        $product = GlassProduct::findOrFail($validated['glassProductId']);
        $product->accessories()->updateExistingOrAttach(
            $validated['accessoryId'],
            ['default_price' => $validated['defaultPrice']]
        );

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function getProductOptions(): array
    {
        return GlassProduct::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getAccessoryOptions(): array
    {
        return Accessory::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getItems()
    {
        $query = GlassProduct::with('accessories');

        if ($this->search !== '') {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return $query->latest()->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.product-accessory-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Product Accessories',
            'header' => 'Product Accessories',
        ]);
    }
}