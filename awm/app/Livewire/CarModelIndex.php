<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\CarBrand;
use App\Models\CarModel;

class CarModelIndex extends \Livewire\Component
{
    use HasCrudOperations;

    public int $carBrandId = 0;
    public string $name = '';
    public string $slug = '';

    public function getModelClass(): string
    {
        return CarModel::class;
    }

    public function getTitle(): string
    {
        return 'Car Model';
    }

    public function getSearchFields(): array
    {
        return ['name', 'slug'];
    }

    public function resetForm(): void
    {
        $this->carBrandId = 0;
        $this->name = '';
        $this->slug = '';
    }

    public function fillFromModel(\Illuminate\Database\Eloquent\Model $record): void
    {
        $this->carBrandId = $record->car_brand_id;
        $this->name = $record->name;
        $this->slug = $record->slug;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'carBrandId' => 'required|exists:car_brands,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:car_models,slug' . ($this->editingId ? ',' . $this->editingId : ''),
        ]);

        $data = [
            'car_brand_id' => $validated['carBrandId'],
            'name' => $validated['name'],
            'slug' => $validated['slug'],
        ];

        if ($this->editingId) {
            CarModel::findOrFail($this->editingId)->update($data);
        } else {
            CarModel::create($data);
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function getBrandOptions(): array
    {
        return CarBrand::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.car-model-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Car Models',
            'header' => 'Car Models',
        ]);
    }
}