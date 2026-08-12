<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\Customer;

class CustomerIndex extends \Livewire\Component
{
    use HasCrudOperations;

    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $notes = '';

    public function getModelClass(): string
    {
        return Customer::class;
    }

    public function getTitle(): string
    {
        return 'Customer';
    }

    public function getSearchFields(): array
    {
        return ['name', 'phone', 'email'];
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->phone = '';
        $this->email = '';
        $this->address = '';
        $this->notes = '';
    }

    public function fillFromModel(\Illuminate\Database\Eloquent\Model $record): void
    {
        $this->name = $record->name;
        $this->phone = $record->phone ?? '';
        $this->email = $record->email ?? '';
        $this->address = $record->address ?? '';
        $this->notes = $record->notes ?? '';
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($this->editingId) {
            Customer::findOrFail($this->editingId)->update($validated);
        } else {
            Customer::create($validated);
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.customer-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Customers',
            'header' => 'Customers',
        ]);
    }
}