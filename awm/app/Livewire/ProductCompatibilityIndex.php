<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\CarModel;
use App\Models\GlassProduct;
use App\Models\ProductCompatibility;

class ProductCompatibilityIndex extends \Livewire\Component
{
    use HasCrudOperations;

    public int $glassProductId = 0;
    public int $carModelId = 0;
    public string $yearFrom = '';
    public string $yearTo = '';

    public function getModelClass(): string
    {
        return ProductCompatibility::class;
    }

    public function getTitle(): string
    {
        return 'Product Compatibility';
    }

    public function getSearchFields(): array
    {
        return [];
    }

    public function resetForm(): void
    {
        $this->glassProductId = 0;
        $this->carModelId = 0;
        $this->yearFrom = '';
        $this->yearTo = '';
    }

    public function fillFromModel(\Illuminate\Database\Eloquent\Model $record): void
    {
        $this->glassProductId = $record->glass_product_id;
        $this->carModelId = $record->car_model_id;
        $this->yearFrom = $record->year_from ? (string) $record->year_from : '';
        $this->yearTo = $record->year_to ? (string) $record->year_to : '';
    }

    public function save(): void
    {
        $validated = $this->validate([
            'glassProductId' => 'required|exists:glass_products,id',
            'carModelId' => 'required|exists:car_models,id',
            'yearFrom' => 'nullable|string|max:4',
            'yearTo' => 'nullable|string|max:4',
        ]);

        $data = [
            'glass_product_id' => $validated['glassProductId'],
            'car_model_id' => $validated['carModelId'],
            'year_from' => $validated['yearFrom'] ?: null,
            'year_to' => $validated['yearTo'] ?: null,
        ];

        if ($this->editingId) {
            ProductCompatibility::findOrFail($this->editingId)->update($data);
        } else {
            ProductCompatibility::create($data);
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function getProductOptions(): array
    {
        return GlassProduct::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getModelOptions(): array
    {
        return CarModel::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getItems()
    {
        $query = ProductCompatibility::with(['glassProduct', 'carModel']);

        if ($this->search !== '') {
            $query->whereHas('glassProduct', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'));
        }

        return $query->latest()->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.product-compatibility-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Product Compatibilities',
            'header' => 'Product Compatibilities',
        ]);
    }
}