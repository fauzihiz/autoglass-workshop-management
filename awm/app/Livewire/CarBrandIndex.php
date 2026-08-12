<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\CarBrand;

class CarBrandIndex extends \Livewire\Component
{
    use HasCrudOperations;

    public string $name = '';
    public string $slug = '';

    public function getModelClass(): string
    {
        return CarBrand::class;
    }

    public function getTitle(): string
    {
        return 'Car Brand';
    }

    public function getSearchFields(): array
    {
        return ['name', 'slug'];
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->slug = '';
    }

    public function fillFromModel(\Illuminate\Database\Eloquent\Model $record): void
    {
        $this->name = $record->name;
        $this->slug = $record->slug;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:car_brands,slug' . ($this->editingId ? ',' . $this->editingId : ''),
        ]);

        if ($this->editingId) {
            CarBrand::findOrFail($this->editingId)->update($validated);
        } else {
            CarBrand::create($validated);
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.car-brand-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Car Brands',
            'header' => 'Car Brands',
        ]);
    }
}