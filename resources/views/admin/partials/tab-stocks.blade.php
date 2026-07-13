{{-- ---- TAB: STOCKS ---- --}}
<div x-show="activeTab === 'stocks'" x-cloak class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Stock Management</h1>
        <p class="text-xs text-gray-500 mt-0.5">Monitor and update inventory levels</p>
    </div>
    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800 text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-950 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Current Stock</th>
                        <th class="px-6 py-4 text-center">Update Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($allProducts as $product)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition"
                        x-data="{
                            inputVal: stocks['{{ $product->id }}'] ?? 0,
                            saved: false,
                            saving: false,
                            update() {
                                const val = parseInt(this.inputVal);
                                if (isNaN(val) || val < 0) { this.inputVal = stocks['{{ $product->id }}']; return; }
                                setStock('{{ $product->id }}', val);
                                this.saved = true;
                                setTimeout(() => this.saved = false, 1500);
                            }
                        }"
                        x-init="$watch('stocks[\'{{ $product->id }}\']', v => { if (document.activeElement !== $refs.stockInput{{ $product->id }}) inputVal = v; })">

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
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Direct number input --}}
                                <input
                                    type="number"
                                    x-ref="stockInput{{ $product->id }}"
                                    x-model.number="inputVal"
                                    min="0"
                                    step="1"
                                    @keydown.enter="update()"
                                    class="w-24 px-3 py-1.5 text-sm text-center font-semibold bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                                    placeholder="0"
                                >
                                {{-- Save button --}}
                                <button
                                    @click="update()"
                                    :class="saved
                                        ? 'bg-green-500 text-white border-green-500'
                                        : 'bg-amber-500 hover:bg-amber-600 text-white border-amber-500'"
                                    class="flex items-center space-x-1 px-3 py-1.5 rounded-xl text-xs font-semibold border transition-all active:scale-95 shrink-0">
                                    <template x-if="!saved">
                                        <span class="flex items-center space-x-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>Set</span>
                                        </span>
                                    </template>
                                    <template x-if="saved">
                                        <span class="flex items-center space-x-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>Saved!</span>
                                        </span>
                                    </template>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
