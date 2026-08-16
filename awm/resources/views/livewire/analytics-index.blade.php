<div>
    <x-page-header title="Analytics Dashboard" subtitle="Business insights, KPIs, and data-driven analytics" />

    {{-- ── Revenue Summary Cards ── --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Revenue This Month</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Revenue Last Month</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($revenueLastMonth, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Revenue This Year</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($revenueThisYear, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
            <p class="text-sm font-medium text-blue-700">Total Revenue (All Time)</p>
            <p class="mt-1 text-2xl font-bold text-blue-700">Rp {{ number_format($revenueAllTime, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- ── Profit and Cost Summary ── --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Glass Cost</p>
            <p class="mt-1 text-2xl font-bold text-amber-700">Rp {{ number_format($totalGlassCost, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Profit</p>
            <p class="mt-1 text-2xl font-bold {{ $totalProfit >= 0 ? 'text-green-700' : 'text-red-700' }}">
                Rp {{ number_format($totalProfit, 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Profit Margin</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $profitMargin }}%</p>
        </div>
    </div>

    {{-- ── Two-column: Sales Trend + Transaction Type ── --}}
    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Sales Trend (Last 6 Months)</h2>
            @if ($monthlyRevenue->every(fn ($m) => $m['amount'] == 0))
                <p class="text-sm text-gray-500">No revenue data available for the last 6 months.</p>
            @else
                <div class="space-y-3">
                    @foreach ($monthlyRevenue as $data)
                        @php $width = $maxMonthlyRevenue > 0 ? round(($data['amount'] / $maxMonthlyRevenue) * 100) : 0; @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-700">{{ $data['month'] }}</span>
                                <span class="text-gray-500">Rp {{ number_format($data['amount'], 0, ',', '.') }}</span>
                            </div>
                            <div class="h-5 w-full overflow-hidden rounded bg-gray-100">
                                <div class="h-full rounded bg-blue-500 transition-all" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Transaction Type Breakdown</h2>
            <div class="space-y-4">
                @foreach ($transactionTypes as $type)
                    <div class="rounded-lg border border-gray-100 p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900">{{ $type['label'] }}</span>
                            <span class="text-sm text-gray-500">{{ $type['count'] }} transaction(s)</span>
                        </div>
                        <div class="mt-2 text-lg font-bold text-gray-900">
                            Rp {{ number_format($type['revenue'], 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Two-column: Best-Selling Glass + Glass Movement ── --}}
    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Best-Selling Glass Products</h2>
            @if ($bestSellingGlass->isEmpty())
                <p class="text-sm text-gray-500">No glass sales recorded yet.</p>
            @else
                <div class="space-y-3">
                    @foreach ($bestSellingGlass as $product)
                        @php $width = $maxUnitsSold > 0 ? round(($product->total_sold / $maxUnitsSold) * 100) : 0; @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-900">{{ $product->name }}</span>
                                <span class="text-gray-500">{{ $product->total_sold }} unit(s) · Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</span>
                            </div>
                            <div class="h-3 w-full overflow-hidden rounded bg-gray-100">
                                <div class="h-full rounded bg-emerald-500 transition-all" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Glass Movement Analysis</h2>
            @php $totalMovement = $stockInTotal + $stockOutTotal + $transferTotal + $adjustmentTotal; @endphp
            @if ($totalMovement === 0)
                <p class="text-sm text-gray-500">No stock movements recorded yet.</p>
            @else
                <div class="space-y-4">
                    @foreach ([
                        ['label' => 'Stock In', 'value' => $stockInTotal, 'color' => 'bg-green-500'],
                        ['label' => 'Stock Out', 'value' => $stockOutTotal, 'color' => 'bg-red-500'],
                        ['label' => 'Transfer', 'value' => $transferTotal, 'color' => 'bg-blue-500'],
                        ['label' => 'Adjustment', 'value' => $adjustmentTotal, 'color' => 'bg-amber-500'],
                    ] as $item)
                        @php $width = $totalMovement > 0 ? round(($item['value'] / $totalMovement) * 100) : 0; @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-700">{{ $item['label'] }}</span>
                                <span class="text-gray-500">{{ $item['value'] }} unit(s) ({{ $width }}%)</span>
                            </div>
                            <div class="h-4 w-full overflow-hidden rounded bg-gray-100">
                                <div class="h-full rounded {{ $item['color'] }} transition-all" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

    {{-- ── Two-column: Customer Ranking + Purchase Frequency ── --}}
    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Customer Ranking (by Spending)</h2>
            @if ($customerRanking->isEmpty())
                <p class="text-sm text-gray-500">No payment data available yet.</p>
            @else
                <div class="space-y-3">
                    @foreach ($customerRanking as $index => $customer)
                        @php $width = $maxCustomerSpent > 0 ? round(($customer->total_spent / $maxCustomerSpent) * 100) : 0; @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-xs font-bold text-gray-700">{{ $index + 1 }}</span>
                                    <a href="{{ route('customers.show', $customer->id) }}" class="font-medium text-blue-600 hover:underline">{{ $customer->name }}</a>
                                </div>
                                <span class="text-gray-500">{{ $customer->total_transactions }} tx · Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</span>
                            </div>
                            <div class="h-3 w-full overflow-hidden rounded bg-gray-100">
                                <div class="h-full rounded bg-purple-500 transition-all" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Purchase Frequency (per Month)</h2>
            @if ($purchaseFrequency->isEmpty())
                <p class="text-sm text-gray-500">No transactions recorded yet.</p>
            @else
                @php $maxFrequency = $purchaseFrequency->max('total') ?: 1; @endphp
                <div class="space-y-3">
                    @foreach ($purchaseFrequency as $data)
                        @php $width = $maxFrequency > 0 ? round(($data->total / $maxFrequency) * 100) : 0; @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-700">{{ $data->month }}</span>
                                <span class="text-gray-500">{{ $data->total }} transaction(s)</span>
                            </div>
                            <div class="h-4 w-full overflow-hidden rounded bg-gray-100">
                                <div class="h-full rounded bg-indigo-500 transition-all" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    {{-- ── Two-column: Fast-Moving + Slow-Moving Products ── --}}
    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Fast-Moving Products</h2>
            @if ($fastMoving->isEmpty())
                <p class="text-sm text-gray-500">No sales allocation data available yet.</p>
            @else
                <div class="space-y-3">
                    @foreach ($fastMoving as $product)
                        @php $width = $maxFastMoving > 0 ? round(($product->total_used / $maxFastMoving) * 100) : 0; @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-900">{{ $product->name }}</span>
                                <span class="text-gray-500">{{ $product->total_used }} unit(s) sold</span>
                            </div>
                            <div class="h-3 w-full overflow-hidden rounded bg-gray-100">
                                <div class="h-full rounded bg-red-500 transition-all" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Slow-Moving Products</h2>
            @if ($slowMoving->isEmpty())
                <p class="text-sm text-gray-500">No product data available yet.</p>
            @else
                <div class="space-y-3">
                    @foreach ($slowMoving as $product)
                        @php $width = $maxSlowMoving > 0 ? round(($product->total_used / $maxSlowMoving) * 100) : 0; @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-900">{{ $product->name }}</span>
                                <span class="text-gray-500">{{ $product->total_used }} unit(s) sold</span>
                            </div>
                            <div class="h-3 w-full overflow-hidden rounded bg-gray-100">
                                <div class="h-full rounded bg-gray-400 transition-all" style="width: max({{ $width }}%, 4%)"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Stock Value Overview ── --}}
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-base font-semibold text-gray-900">Stock Value Overview</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-gray-100 p-4 text-center">
                <p class="text-sm font-medium text-gray-500">Total Stock Value (at Cost)</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-center">
                <p class="text-sm font-medium text-amber-700">Low Stock Products</p>
                <p class="mt-1 text-2xl font-bold text-amber-700">{{ $lowStockCount }}</p>
            </div>
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-center">
                <p class="text-sm font-medium text-red-700">Out of Stock Products</p>
                <p class="mt-1 text-2xl font-bold text-red-700">{{ $outOfStockCount }}</p>
            </div>
        </div>
    </div>
</div>