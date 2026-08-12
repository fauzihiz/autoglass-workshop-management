<?php

namespace App\Livewire\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasCrudOperations
{
    public bool $showModal = false;
    public bool $confirmingDelete = false;
    public ?int $editingId = null;
    public string $search = '';
    public int $perPage = 15;

    abstract public function getModelClass(): string;
    abstract public function getTitle(): string;
    abstract public function getSearchFields(): array;

    public function getItems()
    {
        $query = $this->getModelClass()::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                foreach ($this->getSearchFields() as $field) {
                    $q->orWhere($field, 'like', '%' . $this->search . '%');
                }
            });
        }

        return $query->latest()->paginate($this->perPage);
    }

    public function openCreateModal(): void
    {
        $this->editingId = null;
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(Model $record): void
    {
        $this->editingId = $record->id;
        $this->fillFromModel($record);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(Model $record): void
    {
        $this->editingId = $record->id;
        $this->confirmingDelete = true;
    }

    public function delete(): void
    {
        $this->getModelClass()::findOrFail($this->editingId)->delete();
        $this->confirmingDelete = false;
        $this->editingId = null;
        $this->dispatch('saved');
    }

    abstract public function resetForm(): void;

    abstract public function fillFromModel(Model $record): void;
}