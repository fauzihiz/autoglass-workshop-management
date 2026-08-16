<?php

use Illuminate\Support\Facades\Route;
use App\Livewire;

Route::get('/', fn () => view('dashboard'))->name('dashboard');

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
Route::get('/inventory/movements', Livewire\StockMovementIndex::class)->name('inventory.movements');
Route::get('/inventory/opname', Livewire\StockOpnameIndex::class)->name('inventory.opname');

// ── Transactions ──
Route::get('/transactions', Livewire\TransactionIndex::class)->name('transactions.index');
Route::get('/transactions/create', Livewire\TransactionCreate::class)->name('transactions.create');
Route::get('/transactions/{transaction}', Livewire\TransactionShow::class)->name('transactions.show');
Route::get('/transactions/{transaction}/print', Livewire\InvoicePrint::class)->name('invoices.print');

// ── History & Traceability (Phase 5) ──
Route::get('/payments', Livewire\PaymentIndex::class)->name('payments.index');
Route::get('/complaint-lookup', Livewire\ComplaintLookup::class)->name('complaint-lookup');
Route::get('/customers/{customer}', Livewire\CustomerShow::class)->name('customers.show');
Route::get('/vehicles/{vehicle}', Livewire\VehicleShow::class)->name('vehicles.show');
Route::get('/technicians/{technician}', Livewire\TechnicianShow::class)->name('technicians.show');
Route::get('/glass-products/{glassProduct}', Livewire\GlassProductShow::class)->name('glass-products.show');
Route::get('/inventory/stock-lots/{stockLot}', Livewire\StockLotShow::class)->name('inventory.stock-lots.show');
