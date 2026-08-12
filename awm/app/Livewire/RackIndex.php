<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\Rack;

class RackIndex extends \Livewire\Component
{
    use HasCrudOperations;

    public string $name = '';
    public string $locationDescription = '';

    public function getModelClass(): string
    {
        return Rack::class;
    }

    public function getTitle(): string
    {
        return 'Rack';
    }

    public function getSearchFields(): array
    {
        return ['name', 'location_description'];
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->locationDescription = '';
    }

    public function fillFromModel(\Illuminate\Database\Eloquent\Model $record): void
    {
        $this->name = $record->name;
        $this->locationDescription = $record->location_description ?? '';
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'locationDescription' => 'nullable|string|max:255',
        ]);

        $data = ['name' => $validated['name'], 'location_description' => $validated['locationDescription']];

        if ($this->editingId) {
            Rack::findOrFail($this->editingId)->update($data);
        } else {
            Rack::create($data);
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.rack-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Racks',
            'header' => 'Racks',
        ]);
    }
}