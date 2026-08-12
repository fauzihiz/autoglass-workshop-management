<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\GlassPosition;

class GlassPositionIndex extends \Livewire\Component
{
    use HasCrudOperations;

    public string $name = '';
    public string $code = '';
    public string $description = '';

    public function getModelClass(): string
    {
        return GlassPosition::class;
    }

    public function getTitle(): string
    {
        return 'Glass Position';
    }

    public function getSearchFields(): array
    {
        return ['name', 'code', 'description'];
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->code = '';
        $this->description = '';
    }

    public function fillFromModel(\Illuminate\Database\Eloquent\Model $record): void
    {
        $this->name = $record->name;
        $this->code = $record->code;
        $this->description = $record->description ?? '';
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:glass_positions,code' . ($this->editingId ? ',' . $this->editingId : ''),
            'description' => 'nullable|string|max:255',
        ]);

        if ($this->editingId) {
            GlassPosition::findOrFail($this->editingId)->update($validated);
        } else {
            GlassPosition::create($validated);
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.glass-position-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Glass Positions',
            'header' => 'Glass Positions',
        ]);
    }
}