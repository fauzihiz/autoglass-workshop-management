<?php

namespace App\Livewire;

use App\Models\StockLot;
use Livewire\Component;

class StockLotShow extends Component
{
    public StockLot $stockLot;

    public function mount(StockLot $stockLot): void
    {
        $this->stockLot = $stockLot->load([
            'product',
            'supplier',
            'balances.rack',
            'movements.rack',
            'allocations.transactionItem.transaction',
            'allocations.transactionItem.itemable',
        ]);
    }

    public function render()
    {
        return view('livewire.stock-lot-show')->layout('layouts.app', [
            'title' => $this->stockLot->lot_number,
            'header' => 'Stock Lot Details',
        ]);
    }
}