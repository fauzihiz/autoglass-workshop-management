<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\Service;

class ServiceIndex extends \Livewire\Component
{
    use HasCrudOperations;

    public string $name = '';
    public string $description = '';
    public float $defaultPrice = 0;
    public bool $isActive = true;

    public function getModelClass(): string
    {
        return Service::class;
    }

    public function getTitle(): string
    {
        return 'Service';
    }

    public function getSearchFields(): array
    {
        return ['name', 'description'];
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->description = '';
        $this->defaultPrice = 0;
        $this->isActive = true;
    }

    public function fillFromModel(\Illuminate\Database\Eloquent\Model $record): void
    {
        $this->name = $record->name;
        $this->description = $record->description ?? '';
        $this->defaultPrice = (float) $record->default_price;
        $this->isActive = $record->is_active;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'defaultPrice' => 'required|numeric|min:0',
            'isActive' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'default_price' => $validated['defaultPrice'],
            'is_active' => $validated['isActive'],
        ];

        if ($this->editingId) {
            Service::findOrFail($this->editingId)->update($data);
        } else {
            Service::create($data);
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.service-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Services',
            'header' => 'Services',
        ]);
    }
}