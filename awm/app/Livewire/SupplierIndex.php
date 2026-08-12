<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\Supplier;

class SupplierIndex extends \Livewire\Component
{
    use HasCrudOperations;

    public string $name = '';
    public string $contactPerson = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $notes = '';
    public bool $isActive = true;

    public function getModelClass(): string
    {
        return Supplier::class;
    }

    public function getTitle(): string
    {
        return 'Supplier';
    }

    public function getSearchFields(): array
    {
        return ['name', 'contact_person', 'phone', 'email'];
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->contactPerson = '';
        $this->phone = '';
        $this->email = '';
        $this->address = '';
        $this->notes = '';
        $this->isActive = true;
    }

    public function fillFromModel(\Illuminate\Database\Eloquent\Model $record): void
    {
        $this->name = $record->name;
        $this->contactPerson = $record->contact_person ?? '';
        $this->phone = $record->phone ?? '';
        $this->email = $record->email ?? '';
        $this->address = $record->address ?? '';
        $this->notes = $record->notes ?? '';
        $this->isActive = $record->is_active;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'contactPerson' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
            'isActive' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'contact_person' => $validated['contactPerson'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $validated['isActive'],
        ];

        if ($this->editingId) {
            Supplier::findOrFail($this->editingId)->update($data);
        } else {
            Supplier::create($data);
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.supplier-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Suppliers',
            'header' => 'Suppliers',
        ]);
    }
}