<?php

namespace App\Livewire;

use App\Models\GlassProduct;
use App\Models\TransactionItem;
use Livewire\Component;

class GlassProductShow extends Component
{
    public GlassProduct $glassProduct;

    public function mount(GlassProduct $glassProduct): void
    {
        $this->glassProduct = $glassProduct->load([
            'position',
            'compatibilities.brand',
            'compatibilities.model',
            'stockLots.supplier',
            'stockLots.balances.rack',
        ]);
    }

    public function getTransactionUsage()
    {
        $itemIds = TransactionItem::where('itemable_type', GlassProduct::class)
            ->where('itemable_id', $this->glassProduct->id)
            ->pluck('transaction_id');

        return \App\Models\Transaction::with(['customer', 'vehicle.brand', 'vehicle.model'])
            ->whereIn('id', $itemIds)
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.glass-product-show', [
            'transactionUsage' => $this->getTransactionUsage(),
        ])->layout('layouts.app', [
            'title' => $this->glassProduct->name,
            'header' => 'Glass Product Details',
        ]);
    }
}