<div>
    <x-page-header title="Inventory Dashboard" subtitle="Stock overview, low-stock and out-of-stock warnings">
    </x-page-header>

    {{-- Summary Cards --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Products</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalProducts }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Stock Value (est.)</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <p class="text-sm font-medium text-amber-700">Low Stock Alerts</p>
            <p class="mt-1 text-2xl font-bold text-amber-700">{{ $lowStockCount }}</p>
        </div>
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm">
            <p class="text-sm font-medium text-red-700">Out of Stock</p>
            <p class="mt-1 text-2xl font-bold text-red-700">{{ $outOfStockCount }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name or SKU..." class="block w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        <div class="flex gap-2">
            <button wire:click="$set('filter', 'all')" class="rounded-lg px-3 py-2 text-sm font-medium {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">All</button>
            <button wire:click="$set('filter', 'low')" class="rounded-lg px-3 py-2 text-sm font-medium {{ $filter === 'low' ? 'bg-amber-500 text-white' : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">Low Stock</button>
            <button wire:click="$set('filter', 'out')" class="rounded-lg px-3 py-2 text-sm font-medium {{ $filter === 'out' ? 'bg-red-600 text-white' : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">Out of Stock</button>
        </div>
    </div>

    {{-- Product Stock Table --}}
    <x-data-table empty-text="No products found.">
        <x-slot:header>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">SKU</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Product</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Position</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total Stock</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Min Stock</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Lots</th>
        </x-slot:header>

        @forelse ($products as $product)
            <tr wire:key="inv-{{ $product->id }}">
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500"><x-badge>{{ $product->sku }}</x-badge></td>
                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $product->name }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $product->position->name ?? '—' }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold {{ $product->total_stock === 0 ? 'text-red-600' : ($product->total_stock <= $product->minimum_stock ? 'text-amber-600' : 'text-gray-900') }}">
                    {{ $product->total_stock }}
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $product->minimum_stock }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm">
                    @if ($product->total_stock === 0)
                        <x-badge variant="red">Out of Stock</x-badge>
                    @elseif ($product->total_stock <= $product->minimum_stock)
                        <x-badge variant="amber">Low Stock</x-badge>
                    @else
                        <x-badge variant="green">Normal</x-badge>
                    @endif
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $product->stockLots->count() }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">No products found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $products->links() }}</div>
</div>