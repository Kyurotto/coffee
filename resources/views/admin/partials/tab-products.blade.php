{{-- ---- TAB: PRODUCTS ---- --}}
<div x-show="activeTab === 'products'" x-cloak class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Product Catalog</h1>
        <p class="text-xs text-gray-500 mt-0.5">View and filter items in store</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="flex-1 relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" x-model="searchQuery" placeholder="Search products..."
                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-xl text-sm focus:ring-amber-500 focus:border-amber-500 dark:text-white">
        </div>
        <div class="flex gap-2">
            <button @click="categoryFilter = 'all'" :class="categoryFilter==='all' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-950 text-gray-700 dark:text-gray-300'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition">All</button>
            <button @click="categoryFilter = 'milktea'" :class="categoryFilter==='milktea' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-950 text-gray-700 dark:text-gray-300'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition">Milk Tea</button>
            <button @click="categoryFilter = 'snack'" :class="categoryFilter==='snack' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-950 text-gray-700 dark:text-gray-300'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition">Snacks</button>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="product in filteredProducts" :key="product.id">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                {{-- Product Image --}}
                <div class="relative h-44 w-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                    <template x-if="product.image">
                        <img :src="product.image" :alt="product.name"
                             class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                    </template>
                    <template x-if="!product.image">
                        <div class="w-full h-full flex items-center justify-center"
                             :class="product.category === 'milktea' ? 'bg-gradient-to-br from-blue-100 to-indigo-200 dark:from-blue-900/40 dark:to-indigo-900/40' : 'bg-gradient-to-br from-amber-100 to-orange-200 dark:from-amber-900/40 dark:to-orange-900/40'">
                            <span class="text-4xl font-black opacity-30 text-gray-600 dark:text-gray-300" x-text="product.name.charAt(0)"></span>
                        </div>
                    </template>
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase backdrop-blur-sm bg-white/80 dark:bg-gray-900/80"
                              :class="product.category==='milktea' ? 'text-blue-700' : 'text-amber-700'"
                              x-text="product.category==='milktea' ? 'Milk Tea' : 'Snack'"></span>
                    </div>
                </div>
                {{-- Product Info --}}
                <div class="p-4 flex flex-col flex-1 justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="font-bold text-sm text-gray-900 dark:text-white" x-text="product.name"></h4>
                            <span class="text-sm font-bold text-amber-600 dark:text-amber-400 shrink-0 ml-2" x-text="'$' + parseFloat(product.price).toFixed(2)"></span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2" x-text="product.description"></p>
                    </div>
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800 mt-3 flex justify-between items-center">
                        <span class="text-xs text-gray-400">Stock:</span>
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300" x-text="stocks[product.id]"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
