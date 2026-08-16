<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transition-transform duration-200 lg:translate-x-0 lg:static lg:z-auto">
    <div class="flex h-16 items-center gap-3 border-b border-gray-800 px-6">
        <img src="{{ asset('images/logo.png') }}" alt="AWM" class="h-8 w-8 rounded-lg object-contain">
        <span class="text-sm font-semibold text-white">{{ config('app.name') }}</span>
    </div>
    <nav class="flex-1 overflow-y-auto px-3 py-4">
        <ul class="space-y-1">
            @php
                $d = 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z';
                $links = [
                    ['route' => 'dashboard', 'label' => 'Dashboard', 'svg' => $d],
                    ['route' => 'customers.index', 'label' => 'Customers', 'svg' => $d, 'group' => 'Master Data'],
                    ['route' => 'vehicles.index', 'label' => 'Vehicles', 'svg' => $d],
                    ['route' => 'glass-products.index', 'label' => 'Glass Products', 'svg' => $d],
                    ['route' => 'car-brands.index', 'label' => 'Car Brands', 'svg' => $d],
                    ['route' => 'car-models.index', 'label' => 'Car Models', 'svg' => $d],
                    ['route' => 'glass-positions.index', 'label' => 'Glass Positions', 'svg' => $d],
                    ['route' => 'racks.index', 'label' => 'Racks', 'svg' => $d],
                    ['route' => 'accessories.index', 'label' => 'Accessories', 'svg' => $d],
                    ['route' => 'suppliers.index', 'label' => 'Suppliers', 'svg' => $d],
                    ['route' => 'services.index', 'label' => 'Services', 'svg' => $d],
                    ['route' => 'product-compatibilities.index', 'label' => 'Compatibilities', 'svg' => $d],
                    ['route' => 'product-accessories.index', 'label' => 'Product Accessories', 'svg' => $d],
                    ['route' => 'technicians.index', 'label' => 'Technicians', 'svg' => $d],
                    ['route' => 'inventory.index', 'label' => 'Inventory Dashboard', 'svg' => $d, 'group' => 'Inventory'],
                    ['route' => 'inventory.stock-lots', 'label' => 'Stock Lots', 'svg' => $d],
                    ['route' => 'inventory.stock-in', 'label' => 'Stock In', 'svg' => $d],
                    ['route' => 'inventory.stock-transfer', 'label' => 'Stock Transfer', 'svg' => $d],
                    ['route' => 'inventory.movements', 'label' => 'Stock Movements', 'svg' => $d],
                    ['route' => 'inventory.opname', 'label' => 'Stock Opname', 'svg' => $d],
                ];
            @endphp
            @foreach ($links as $link)
                @if (isset($link['group']))
                    <li class="pt-4"><span class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $link['group'] }}</span></li>
                @endif
                <li><a href="{{ route($link['route']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($link['route']) ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}"><svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['svg'] }}" /></svg>{{ $link['label'] }}</a></li>
            @endforeach
            <li class="pt-4"><span class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Transactions</span></li>
            <li><a href="{{ route('transactions.create') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-800/50 hover:text-white"><svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659 1.398-1.14 1.398 1.14.879-.659M12 6a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z" /></svg>New Transaction</a></li>
            <li><a href="{{ route('transactions.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-800/50 hover:text-white"><svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>Transaction History</a></li>
            <li class="pt-4"><span class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Analytics</span></li>
            <li><a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-800/50 hover:text-white"><svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>Reports</a></li>
        </ul>
    </nav>
</aside>