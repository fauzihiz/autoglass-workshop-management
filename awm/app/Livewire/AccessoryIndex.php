<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\Accessory;

class AccessoryIndex extends \Livewire\Component
{
    use HasCrudOperations;

    public string $name = '';
    public string $description = '';

    public function getModelClass(): string
    {
        return Accessory::class;
    }

    public function getTitle(): string
    {
        return 'Accessory';
    }

    public function getSearchFields(): array
    {
        return ['name', 'description'];
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->description = '';
    }

    public function fillFromModel(\Illuminate\Database\Eloquent\Model $record): void
    {
        $this->name = $record->name;
        $this->description = $record->description ?? '';
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        if ($this->editingId) {
            Accessory::findOrFail($this->editingId)->update($validated);
        } else {
            Accessory::create($validated);
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.accessory-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Accessories',
            'header' => 'Accessories',
        ]);
    }
}