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
