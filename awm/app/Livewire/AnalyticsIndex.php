<?php

namespace App\Livewire;

use App\Enums\StockMovementType;
use App\Enums\TransactionType;
use App\Models\Customer;
use App\Models\GlassProduct;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AnalyticsIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        // ── Revenue Summary ──
        $revenueThisMonth = (float) Payment::whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $revenueLastMonth = (float) Payment::whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('amount');

        $revenueThisYear = (float) Payment::whereYear('paid_at', now()->year)->sum('amount');
        $revenueAllTime = (float) Payment::sum('amount');

        // ── Transaction Type Breakdown ──
        $typeBreakdown = Transaction::select('type', DB::raw('count(*) as total_count'))
            ->groupBy('type')
            ->pluck('total_count', 'type');

        $typeRevenue = Payment::join('transactions', 'transactions.id', '=', 'payments.transaction_id')
            ->select('transactions.type', DB::raw('sum(payments.amount) as total_revenue'))
            ->groupBy('transactions.type')
            ->pluck('total_revenue', 'type');

        $transactionTypes = collect(TransactionType::cases())->map(fn ($t) => [
            'type' => $t->value,
            'label' => $t->label(),
            'count' => $typeBreakdown->get($t->value, 0),
            'revenue' => (float) $typeRevenue->get($t->value, 0),
        ]);

        // ── Profit Summary ──
        $totalGlassCost = (float) DB::table('stock_allocations')
            ->join('stock_lots', 'stock_lots.id', '=', 'stock_allocations.stock_lot_id')
            ->join('transaction_items', 'transaction_items.id', '=', 'stock_allocations.transaction_item_id')
            ->where('transaction_items.itemable_type', GlassProduct::class)
            ->sum(DB::raw('stock_lots.purchase_cost * stock_allocations.quantity'));

        $totalProfit = $revenueAllTime - $totalGlassCost;
        $profitMargin = $revenueAllTime > 0 ? round(($totalProfit / $revenueAllTime) * 100, 1) : 0;

        // ── Monthly Revenue Trend (last 6 months) ──
        $monthlyRevenue = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $amount = (float) Payment::whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('amount');
            $monthlyRevenue->push(['month' => $date->format('M Y'), 'amount' => $amount]);
        }
        $maxMonthlyRevenue = $monthlyRevenue->max('amount') ?: 1;

        // ── Best-Selling Glass Products (top 10) ──
        $bestSellingGlass = TransactionItem::where('itemable_type', GlassProduct::class)
            ->join('glass_products', 'glass_products.id', '=', 'transaction_items.itemable_id')
            ->select(
                'glass_products.id', 'glass_products.name', 'glass_products.sku',
                DB::raw('sum(transaction_items.quantity) as total_sold'),
                DB::raw('sum(transaction_items.total_price) as total_revenue'),
            )
            ->groupBy('glass_products.id', 'glass_products.name', 'glass_products.sku')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();
        $maxUnitsSold = $bestSellingGlass->max('total_sold') ?: 1;

        // ── Glass Movement Analysis ──
        $movementCounts = StockMovement::select('type', DB::raw('sum(quantity) as total_quantity'))
            ->groupBy('type')
            ->pluck('total_quantity', 'type');

        $stockInTotal = (int) $movementCounts->get(StockMovementType::In->value, 0);
        $stockOutTotal = (int) $movementCounts->get(StockMovementType::Out->value, 0);
        $transferTotal = (int) $movementCounts->get(StockMovementType::Transfer->value, 0);
        $adjustmentTotal = (int) $movementCounts->get(StockMovementType::Adjustment->value, 0);

        // ── Customer Ranking (top 10 by spending) ──
        $customerRanking = Customer::join('transactions', 'customers.id', '=', 'transactions.customer_id')
            ->join('payments', 'transactions.id', '=', 'payments.transaction_id')
            ->select(
                'customers.id', 'customers.name',
                DB::raw('count(distinct transactions.id) as total_transactions'),
                DB::raw('sum(payments.amount) as total_spent'),
            )
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();
        $maxCustomerSpent = $customerRanking->max('total_spent') ?: 1;

        // ── Purchase Frequency (transactions per month) ──
        $purchaseFrequency = Transaction::select(
            DB::raw("strftime('%Y-%m', created_at) as month"),
            DB::raw('count(*) as total'),
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ── Stock Analytics ──
        $totalStockValue = (float) DB::table('stock_balances')
            ->join('stock_lots', 'stock_lots.id', '=', 'stock_balances.stock_lot_id')
            ->sum(DB::raw('stock_lots.purchase_cost * stock_balances.quantity'));

        $allProducts = GlassProduct::where('is_active', true)->with('stockLots')->get()->map(function ($p) {
            $p->total_stock;
            return $p;
        });
        $lowStockCount = $allProducts->filter(fn ($p) => $p->total_stock > 0 && $p->total_stock <= $p->minimum_stock)->count();
        $outOfStockCount = $allProducts->filter(fn ($p) => $p->total_stock === 0)->count();

        // Fast-moving: products with most stock allocations (units used in sales)
        $fastMoving = DB::table('stock_allocations')
            ->join('transaction_items', 'transaction_items.id', '=', 'stock_allocations.transaction_item_id')
            ->join('glass_products', 'glass_products.id', '=', 'transaction_items.itemable_id')
            ->select(
                'glass_products.id', 'glass_products.name', 'glass_products.sku',
                DB::raw('sum(stock_allocations.quantity) as total_used'),
            )
            ->where('transaction_items.itemable_type', GlassProduct::class)
            ->groupBy('glass_products.id', 'glass_products.name', 'glass_products.sku')
            ->orderByDesc('total_used')
            ->limit(5)
            ->get();
        $maxFastMoving = $fastMoving->max('total_used') ?: 1;

        // Slow-moving: products with zero or fewest stock-out movements
        $slowMoving = GlassProduct::where('is_active', true)
            ->leftJoin('stock_lots', 'glass_products.id', '=', 'stock_lots.glass_product_id')
            ->leftJoin('stock_movements', function ($join) {
                $join->on('stock_lots.id', '=', 'stock_movements.stock_lot_id')
                    ->where('stock_movements.type', '=', StockMovementType::Out->value);
            })
            ->select(
                'glass_products.id', 'glass_products.name', 'glass_products.sku',
                DB::raw('coalesce(sum(stock_movements.quantity), 0) as total_used'),
            )
            ->groupBy('glass_products.id', 'glass_products.name', 'glass_products.sku')
            ->orderBy('total_used')
            ->limit(5)
            ->get();
        $maxSlowMoving = $slowMoving->max('total_used') ?: 1;

        return view('livewire.analytics-index', [
            'revenueThisMonth' => $revenueThisMonth,
            'revenueLastMonth' => $revenueLastMonth,
            'revenueThisYear' => $revenueThisYear,
            'revenueAllTime' => $revenueAllTime,
            'transactionTypes' => $transactionTypes,
            'totalRevenue' => $revenueAllTime,
            'totalGlassCost' => $totalGlassCost,
            'totalProfit' => $totalProfit,
            'profitMargin' => $profitMargin,
            'monthlyRevenue' => $monthlyRevenue,
            'maxMonthlyRevenue' => $maxMonthlyRevenue,
            'bestSellingGlass' => $bestSellingGlass,
            'maxUnitsSold' => $maxUnitsSold,
            'stockInTotal' => $stockInTotal,
            'stockOutTotal' => $stockOutTotal,
            'transferTotal' => $transferTotal,
            'adjustmentTotal' => $adjustmentTotal,
            'customerRanking' => $customerRanking,
            'maxCustomerSpent' => $maxCustomerSpent,
            'purchaseFrequency' => $purchaseFrequency,
            'totalStockValue' => $totalStockValue,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'fastMoving' => $fastMoving,
            'maxFastMoving' => $maxFastMoving,
            'slowMoving' => $slowMoving,
            'maxSlowMoving' => $maxSlowMoving,
        ])->layout('layouts.app', [
            'title' => 'Analytics Dashboard',
            'header' => 'Analytics Dashboard',
        ]);
    }
}