<?php

namespace App\Livewire;

use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class StockOutIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function render()
    {
        $query = StockMovement::with(['lot.product', 'rack'])
            ->where('type', 'out')
            ->latest();

        if ($this->search !== '') {
            $query->whereHas('lot', function ($q) {
                $q->where('lot_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('product', function ($q2) {
                        $q2->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('sku', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $items = $query->paginate(15);

        return view('livewire.stock-out-index', [
            'items' => $items,
        ])->layout('layouts.app', [
            'title' => 'Stock Out',
            'header' => 'Stock Out',
        ]);
    }
}