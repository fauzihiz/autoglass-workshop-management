<?php

namespace App\Livewire;

use App\Models\GlassProduct;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryDashboardIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'all'; // all, low, out

    public function render()
    {
        $query = GlassProduct::query()->with('position')->where('is_active', true);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        $products = $query->get()->map(function ($product) {
            $product->total_stock;
            return $product;
        });

        // Apply status filter after computing total_stock
        if ($this->filter === 'low') {
            $products = $products->filter(fn ($p) => $p->total_stock > 0 && $p->total_stock <= $p->minimum_stock);
        } elseif ($this->filter === 'out') {
            $products = $products->filter(fn ($p) => $p->total_stock === 0);
        }

        // Summary stats
        $totalProducts = GlassProduct::where('is_active', true)->count();
        $totalStockValue = $products->sum(fn ($p) => $p->stockLots->sum(fn ($lot) => $lot->purchase_cost * $lot->totalQuantity));
        $lowStockCount = $products->filter(fn ($p) => $p->total_stock > 0 && $p->total_stock <= $p->minimum_stock)->count();
        $outOfStockCount = $products->filter(fn ($p) => $p->total_stock === 0)->count();

        $products = $products->paginate(15);

        return view('livewire.inventory-dashboard-index', [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'totalStockValue' => $totalStockValue,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
        ])->layout('layouts.app', [
            'title' => 'Inventory Dashboard',
            'header' => 'Inventory Dashboard',
        ]);
    }
}