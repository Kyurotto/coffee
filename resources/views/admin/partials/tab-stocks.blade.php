{{-- ---- TAB: STOCKS ---- --}}
<div x-show="activeTab === 'stocks'" x-cloak class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Stock Management</h1>
        <p class="text-xs text-gray-500 mt-0.5">Monitor and adjust inventory levels</p>
    </div>
    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800 text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-950 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Stock</th>
                        <th class="px-6 py-4 text-right">Adjust</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($allProducts as $product)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $product->name }}</div>
                            <div class="text-xs text-gray-400">${{ number_format($product->price, 2) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold uppercase {{ $product->category === 'milktea' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400' }}">
                                {{ $product->category === 'milktea' ? 'Milk Tea' : 'Snack' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span x-show="stocks['{{ $product->id }}'] === 0" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">Out of Stock</span>
                            <span x-show="stocks['{{ $product->id }}'] > 0 && stocks['{{ $product->id }}'] < 10" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">Low Stock</span>
                            <span x-show="stocks['{{ $product->id }}'] >= 10" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">In Stock</span>
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-base" x-text="stocks['{{ $product->id }}']"></td>
                        <td class="px-6 py-4 text-right space-x-1">
                            <button @click="adjustStock('{{ $product->id }}', -5)" class="px-2 py-1 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-white rounded text-xs font-bold transition">-5</button>
                            <button @click="adjustStock('{{ $product->id }}', -1)" class="px-2 py-1 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 text-red-600 rounded text-xs font-bold transition">-1</button>
                            <button @click="adjustStock('{{ $product->id }}', 1)" class="px-2 py-1 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 text-green-600 rounded text-xs font-bold transition">+1</button>
                            <button @click="adjustStock('{{ $product->id }}', 5)" class="px-2 py-1 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-white rounded text-xs font-bold transition">+5</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
