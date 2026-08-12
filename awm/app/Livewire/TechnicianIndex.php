<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\Technician;

class TechnicianIndex extends \Livewire\Component
{
    use HasCrudOperations;

    public string $name = '';
    public string $phone = '';
    public bool $isActive = true;

    public function getModelClass(): string
    {
        return Technician::class;
    }

    public function getTitle(): string
    {
        return 'Technician';
    }

    public function getSearchFields(): array
    {
        return ['name', 'phone'];
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->phone = '';
        $this->isActive = true;
    }

    public function fillFromModel(\Illuminate\Database\Eloquent\Model $record): void
    {
        $this->name = $record->name;
        $this->phone = $record->phone ?? '';
        $this->isActive = $record->is_active;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'isActive' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['isActive'],
        ];

        if ($this->editingId) {
            Technician::findOrFail($this->editingId)->update($data);
        } else {
            Technician::create($data);
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.technician-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Technicians',
            'header' => 'Technicians',
        ]);
    }
}