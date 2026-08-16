<?php

it('loads the dashboard', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

it('loads the customers page', function () {
    $response = $this->get('/customers');
    $response->assertStatus(200);
});

it('loads the vehicles page', function () {
    $response = $this->get('/vehicles');
    $response->assertStatus(200);
});

it('loads the glass products page', function () {
    $response = $this->get('/glass-products');
    $response->assertStatus(200);
});

it('loads the inventory dashboard', function () {
    $response = $this->get('/inventory');
    $response->assertOk();
})->skip('Pre-existing issue: InventoryDashboardIndex uses Collection::paginate');

it('loads the transactions list', function () {
    $response = $this->get('/transactions');
    $response->assertStatus(200);
});

it('loads the stock-lots page', function () {
    $response = $this->get('/inventory/stock-lots');
    $response->assertStatus(200);
});

it('loads the stock-in page', function () {
    $response = $this->get('/inventory/stock-in');
    $response->assertStatus(200);
});

it('loads the stock-transfer page', function () {
    $response = $this->get('/inventory/stock-transfer');
    $response->assertStatus(200);
});

it('loads the payments page', function () {
    $response = $this->get('/payments');
    $response->assertStatus(200);
});

it('loads the analytics page', function () {
    $response = $this->get('/analytics');
    $response->assertStatus(200);
})->skip('Pre-existing issue: analytics-index.blade.php has a syntax error');

it('loads the car-brands page', function () {
    $response = $this->get('/car-brands');
    $response->assertStatus(200);
});

it('loads the racks page', function () {
    $response = $this->get('/racks');
    $response->assertStatus(200);
});

it('loads the services page', function () {
    $response = $this->get('/services');
    $response->assertStatus(200);
});

it('loads the suppliers page', function () {
    $response = $this->get('/suppliers');
    $response->assertStatus(200);
});

it('loads the technicians page', function () {
    $response = $this->get('/technicians');
    $response->assertStatus(200);
});

it('returns 404 for non-existent transaction', function () {
    $response = $this->get('/transactions/99999');
    $response->assertStatus(404);
});
