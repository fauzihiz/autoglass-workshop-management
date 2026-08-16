<?php

namespace App\Livewire;

use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class StockMovementIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $typeFilter = '';
    public int $perPage = 15;

    public function render()
    {
        $query = StockMovement::with(['lot.product', 'rack']);

        if ($this->search !== '') {
            $query->whereHas('lot', function ($q) {
                $q->where('lot_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('product', function ($q2) {
                        $q2->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('sku', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->typeFilter !== '') {
            $query->where('type', $this->typeFilter);
        }

        $items = $query->latest()->paginate($this->perPage);

        return view('livewire.stock-movement-index', [
            'items' => $items,
        ])->layout('layouts.app', [
            'title' => 'Stock Movements',
            'header' => 'Stock Movements',
        ]);
    }
}