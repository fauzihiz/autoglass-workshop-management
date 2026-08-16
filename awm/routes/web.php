<?php

use App\Models\Customer;
use App\Models\GlassProduct;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use App\Livewire;

Route::get('/', function () {
    $totalCustomers = Customer::count();
    $totalProducts = GlassProduct::where('is_active', true)->count();
    $transactionsThisMonth = Transaction::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();
    $revenueThisMonth = (float) Payment::whereMonth('paid_at', now()->month)
        ->whereYear('paid_at', now()->year)
        ->sum('amount');

    $recentTransactions = Transaction::with(['customer', 'vehicle'])
        ->latest()
        ->limit(5)
        ->get();

    // Stock alerts: products at or below minimum stock
    $allProducts = GlassProduct::where('is_active', true)->with('stockLots')->get()->map(function ($p) {
        $p->total_stock;
        return $p;
    });
    $lowStockCount = $allProducts->filter(fn ($p) => $p->total_stock > 0 && $p->total_stock <= $p->minimum_stock)->count();
    $outOfStockCount = $allProducts->filter(fn ($p) => $p->total_stock === 0)->count();
    $stockAlerts = $allProducts->filter(fn ($p) => $p->total_stock <= $p->minimum_stock)->take(5);

    return view('dashboard', compact(
        'totalCustomers',
        'totalProducts',
        'transactionsThisMonth',
        'revenueThisMonth',
        'recentTransactions',
        'lowStockCount',
        'outOfStockCount',
        'stockAlerts',
    ));
})->name('dashboard');

// ── Master Data (Livewire Full-Page Components) ──
Route::get('/customers', Livewire\CustomerIndex::class)->name('customers.index');
Route::get('/vehicles', Livewire\VehicleIndex::class)->name('vehicles.index');
Route::get('/car-brands', Livewire\CarBrandIndex::class)->name('car-brands.index');
Route::get('/car-models', Livewire\CarModelIndex::class)->name('car-models.index');
Route::get('/glass-positions', Livewire\GlassPositionIndex::class)->name('glass-positions.index');
Route::get('/glass-products', Livewire\GlassProductIndex::class)->name('glass-products.index');
Route::get('/racks', Livewire\RackIndex::class)->name('racks.index');
Route::get('/services', Livewire\ServiceIndex::class)->name('services.index');
Route::get('/accessories', Livewire\AccessoryIndex::class)->name('accessories.index');
Route::get('/suppliers', Livewire\SupplierIndex::class)->name('suppliers.index');
Route::get('/technicians', Livewire\TechnicianIndex::class)->name('technicians.index');
Route::get('/product-compatibilities', Livewire\ProductCompatibilityIndex::class)->name('product-compatibilities.index');
Route::get('/product-accessories', Livewire\ProductAccessoryIndex::class)->name('product-accessories.index');

// ── Inventory Management ──
Route::get('/inventory', Livewire\InventoryDashboardIndex::class)->name('inventory.index');
Route::get('/inventory/stock-lots', Livewire\StockLotIndex::class)->name('inventory.stock-lots');
Route::get('/inventory/stock-in', Livewire\StockInIndex::class)->name('inventory.stock-in');
Route::get('/inventory/stock-transfer', Livewire\StockTransferIndex::class)->name('inventory.stock-transfer');
Route::get('/inventory/stock-out', Livewire\StockOutIndex::class)->name('inventory.stock-out');
Route::get('/inventory/movements', Livewire\StockMovementIndex::class)->name('inventory.movements');
Route::get('/inventory/opname', Livewire\StockOpnameIndex::class)->name('inventory.opname');

// ── Transactions ──
Route::get('/transactions', Livewire\TransactionIndex::class)->name('transactions.index');
Route::get('/transactions/create', Livewire\TransactionCreate::class)->name('transactions.create');
Route::get('/transactions/{transaction}', Livewire\TransactionShow::class)->name('transactions.show');
Route::get('/transactions/{transaction}/print', Livewire\InvoicePrint::class)->name('invoices.print');

// ── Analytics (Phase 6) ──
Route::get('/analytics', Livewire\AnalyticsIndex::class)->name('analytics.index');

// ── History & Traceability (Phase 5) ──
Route::get('/payments', Livewire\PaymentIndex::class)->name('payments.index');
Route::get('/complaint-lookup', Livewire\ComplaintLookup::class)->name('complaint-lookup');
Route::get('/customers/{customer}', Livewire\CustomerShow::class)->name('customers.show');
Route::get('/vehicles/{vehicle}', Livewire\VehicleShow::class)->name('vehicles.show');
Route::get('/technicians/{technician}', Livewire\TechnicianShow::class)->name('technicians.show');
Route::get('/glass-products/{glassProduct}', Livewire\GlassProductShow::class)->name('glass-products.show');
Route::get('/inventory/stock-lots/{stockLot}', Livewire\StockLotShow::class)->name('inventory.stock-lots.show');
