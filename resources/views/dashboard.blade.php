<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel Coffee') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased h-full bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
        @php
            use Illuminate\Support\Facades\Storage;
            $allProducts = $milkteas->concat($snacks);
            $role = Auth::user()->employee ? Auth::user()->employee->role : 'employee';
        @endphp

        @if($role === 'admin')
        {{-- ==================== ADMIN VIEW ==================== --}}
        <div class="h-full flex flex-col md:flex-row overflow-hidden" x-data="{
            activeTab: 'dashboard',
            sidebarOpen: false,
            searchQuery: '',
            categoryFilter: 'all',

            // Live order stats (start with server-rendered values, poll for updates)
            totalOrders:  {{ $totalOrders ?? 0 }},
            totalRevenue: '{{ number_format($totalRevenue ?? 0, 2) }}',
            todayRevenue: '{{ number_format($todayRevenue ?? 0, 2) }}',
            todayOrders:  {{ $todayOrders ?? 0 }},
            recentOrders: [],

            stocks: {
                @foreach($allProducts as $product)
                    '{{ $product->id }}': {{ $product->id % 3 == 0 ? rand(1, 5) : ($product->id % 5 == 0 ? 0 : rand(15, 50)) }},
                @endforeach
            },

            adjustStock(id, amount) {
                this.stocks[id] = Math.max(0, this.stocks[id] + amount);
            },

            // Poll the summary endpoint every 15 seconds so any device sees live totals
            async pollSummary() {
                try {
                    const res  = await fetch('{{ route('orders.summary') }}', {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.totalOrders  = data.total_orders;
                    this.totalRevenue = data.total_revenue;
                    this.todayRevenue = data.today_revenue;
                    this.todayOrders  = data.today_orders;
                    this.recentOrders = data.recent_orders;
                } catch (e) {}
            },

            init() {
                this.pollSummary();
                setInterval(() => this.pollSummary(), 15000);
            },

            get filteredProducts() {
                let products = [
                    @foreach($allProducts as $product)
                    {
                        id: '{{ $product->id }}',
                        name: '{{ addslashes($product->name) }}',
                        description: '{{ addslashes($product->description) }}',
                        price: {{ $product->price }},
                        category: '{{ $product->category }}',
                        image: '{{ $product->image_path ? Storage::url($product->image_path) : '' }}',
                    },
                    @endforeach
                ];
                return products.filter(p => {
                    const matchesSearch = p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                          p.description.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesCategory = this.categoryFilter === 'all' || p.category === this.categoryFilter;
                    return matchesSearch && matchesCategory;
                });
            }
        }">

            {{-- Mobile top bar --}}
            <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-6 py-4 md:hidden shrink-0">
                <div class="flex items-center space-x-2">
                    <div class="bg-amber-500 p-1.5 rounded-lg text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z"/>
                        </svg>
                    </div>
                    <span class="text-base font-bold text-gray-800 dark:text-white">Coffee Admin</span>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 dark:text-gray-400 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </header>

            {{-- Mobile overlay --}}
            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
                 class="fixed inset-0 z-20 bg-black bg-opacity-50 md:hidden"></div>

            {{-- Sidebar --}}
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
                   class="fixed md:static inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col transition-transform duration-300 ease-in-out shrink-0 h-full">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800 hidden md:block">
                    <div class="flex items-center space-x-3">
                        <div class="bg-amber-500 p-2 rounded-lg text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-800 dark:text-white">Coffee Admin</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Management Panel</p>
                        </div>
                    </div>
                </div>

                <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                    @php
                        $navItems = [
                            ['tab' => 'dashboard', 'label' => 'Dashboard',  'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z'],
                            ['tab' => 'orders',    'label' => 'Orders',      'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                            ['tab' => 'products',  'label' => 'Products',    'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                            ['tab' => 'stocks',    'label' => 'Stocks',      'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                            ['tab' => 'reports',   'label' => 'Reports',     'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2'],
                        ];
                    @endphp

                    @foreach($navItems as $nav)
                    <button @click="activeTab = '{{ $nav['tab'] }}'; sidebarOpen = false"
                            :class="activeTab === '{{ $nav['tab'] }}' ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50'"
                            class="flex items-center space-x-3 w-full px-4 py-3 rounded-xl transition-all duration-200 font-medium text-sm">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $nav['icon'] }}"/>
                        </svg>
                        <span>{{ $nav['label'] }}</span>
                        @if($nav['tab'] === 'orders')
                        <span class="ml-auto bg-amber-500/20 text-amber-600 dark:text-amber-300 text-[10px] font-bold px-1.5 py-0.5 rounded-full" x-text="todayOrders"></span>
                        @endif
                    </button>
                    @endforeach
                </nav>

                <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50 flex flex-col space-y-3">
                    <div class="flex items-center space-x-3 px-2">
                        <div class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold text-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-800 dark:text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 dark:bg-red-950/20 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl text-xs font-semibold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Log Out</span>
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Main content --}}
            <main class="flex-1 h-full overflow-y-auto bg-gray-50 dark:bg-gray-950">
                <div class="p-4 md:p-8 space-y-6">

                    {{-- ---- TAB: DASHBOARD ---- --}}
                    <div x-show="activeTab === 'dashboard'" x-cloak class="space-y-6">
                        <div class="flex flex-wrap justify-between items-center gap-3">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Overview</h1>
                                <p class="text-xs text-gray-500 mt-0.5">Live stats — refreshes every 15 seconds</p>
                            </div>
                            <span class="text-xs text-gray-500 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 px-3 py-1.5 rounded-xl font-medium">{{ now()->format('F d, Y') }}</span>
                        </div>

                        {{-- Stat cards --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            {{-- Total Products --}}
                            <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800/80 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Products</p>
                                    <h3 class="text-3xl font-extrabold text-gray-800 dark:text-white mt-1">{{ $allProducts->count() }}</h3>
                                </div>
                                <div class="bg-blue-500/10 p-3 rounded-xl text-blue-600 dark:text-blue-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                            </div>
                            {{-- Today's Orders --}}
                            <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800/80 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Today's Orders</p>
                                    <h3 class="text-3xl font-extrabold text-amber-600 dark:text-amber-400 mt-1" x-text="todayOrders">{{ $todayOrders }}</h3>
                                </div>
                                <div class="bg-amber-500/10 p-3 rounded-xl text-amber-600 dark:text-amber-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                            </div>
                            {{-- Today's Revenue --}}
                            <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800/80 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Today's Revenue</p>
                                    <h3 class="text-3xl font-extrabold text-green-600 dark:text-green-400 mt-1" x-text="'$' + todayRevenue">${{ number_format($todayRevenue, 2) }}</h3>
                                </div>
                                <div class="bg-green-500/10 p-3 rounded-xl text-green-600 dark:text-green-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16V5"/></svg>
                                </div>
                            </div>
                            {{-- Total Revenue All-Time --}}
                            <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800/80 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">All-Time Revenue</p>
                                    <h3 class="text-3xl font-extrabold text-purple-600 dark:text-purple-400 mt-1" x-text="'$' + totalRevenue">${{ number_format($totalRevenue, 2) }}</h3>
                                </div>
                                <div class="bg-purple-500/10 p-3 rounded-xl text-purple-600 dark:text-purple-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Recent orders live feed + quick activities --}}
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            {{-- Live recent orders --}}
                            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800/80 lg:col-span-2 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                                    <h3 class="font-bold text-gray-800 dark:text-white">Recent Orders</h3>
                                    <span class="flex items-center space-x-1.5 text-[10px] text-green-600 font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span>
                                        <span>Live</span>
                                    </span>
                                </div>

                                {{-- Empty state --}}
                                <template x-if="recentOrders.length === 0">
                                    <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                                        <svg class="w-10 h-10 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <p class="text-sm">No orders yet today</p>
                                    </div>
                                </template>

                                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                                    <template x-for="order in recentOrders" :key="order.id">
                                        <div class="px-6 py-3.5 flex items-center justify-between hover:bg-gray-50/60 dark:hover:bg-gray-800/30 transition">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs font-bold text-gray-800 dark:text-white" x-text="order.receipt_no"></span>
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400" x-text="order.status"></span>
                                                </div>
                                                <p class="text-[11px] text-gray-400 mt-0.5">
                                                    Cashier: <span class="font-medium text-gray-600 dark:text-gray-300" x-text="order.cashier"></span>
                                                    &bull; <span x-text="order.time"></span>
                                                </p>
                                                <p class="text-[11px] text-gray-400 truncate" x-text="order.items.map(i => i.name + ' x' + i.quantity).join(', ')"></p>
                                            </div>
                                            <span class="ml-4 text-sm font-extrabold text-amber-600 dark:text-amber-400 shrink-0" x-text="'$' + order.total"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Quick summary sidebar --}}
                            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800/80 flex flex-col space-y-5">
                                <h3 class="font-bold text-gray-800 dark:text-white">Quick Summary</h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">All-time orders</span>
                                        <span class="font-bold text-gray-800 dark:text-white" x-text="totalOrders + ' orders'">{{ $totalOrders }} orders</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">Today's orders</span>
                                        <span class="font-bold text-amber-600" x-text="todayOrders + ' orders'">{{ $todayOrders }} orders</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">Today's revenue</span>
                                        <span class="font-bold text-green-600" x-text="'$' + todayRevenue">${{ number_format($todayRevenue, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">All-time revenue</span>
                                        <span class="font-bold text-purple-600" x-text="'$' + totalRevenue">${{ number_format($totalRevenue, 2) }}</span>
                                    </div>
                                </div>
                                <div class="border-t border-dashed border-gray-200 dark:border-gray-800 pt-4">
                                    <button @click="activeTab = 'orders'" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2.5 rounded-xl text-xs transition shadow-sm">
                                        View All Orders →
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ---- TAB: ORDERS ---- --}}
                    <div x-show="activeTab === 'orders'" x-cloak class="space-y-6">
                        <div class="flex flex-wrap justify-between items-center gap-3">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Order History</h1>
                                <p class="text-xs text-gray-500 mt-0.5">All customer orders placed by cashiers</p>
                            </div>
                            <div class="flex items-center space-x-2 text-xs text-green-600 font-semibold bg-green-50 dark:bg-green-900/20 px-3 py-1.5 rounded-xl">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span>
                                <span>Auto-refreshing</span>
                            </div>
                        </div>

                        {{-- Summary bar --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Total Orders</p>
                                <p class="text-2xl font-extrabold text-gray-800 dark:text-white mt-1" x-text="totalOrders">{{ $totalOrders }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Today's Orders</p>
                                <p class="text-2xl font-extrabold text-amber-600 mt-1" x-text="todayOrders">{{ $todayOrders }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Today Revenue</p>
                                <p class="text-2xl font-extrabold text-green-600 mt-1" x-text="'$' + todayRevenue">${{ number_format($todayRevenue, 2) }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wider">All-Time Revenue</p>
                                <p class="text-2xl font-extrabold text-purple-600 mt-1" x-text="'$' + totalRevenue">${{ number_format($totalRevenue, 2) }}</p>
                            </div>
                        </div>

                        {{-- Order table --}}
                        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                            <template x-if="recentOrders.length === 0">
                                <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                                    <svg class="w-12 h-12 mb-3 opacity-25" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="text-sm font-medium">No orders recorded yet</p>
                                    <p class="text-xs mt-1 text-gray-300">Orders from cashiers will appear here</p>
                                </div>
                            </template>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800 text-sm" x-show="recentOrders.length > 0">
                                    <thead class="bg-gray-50 dark:bg-gray-950 text-xs text-gray-500 uppercase tracking-wider">
                                        <tr>
                                            <th class="px-6 py-4 text-left">Receipt</th>
                                            <th class="px-6 py-4 text-left">Cashier</th>
                                            <th class="px-6 py-4 text-left">Items</th>
                                            <th class="px-6 py-4 text-left">Time</th>
                                            <th class="px-6 py-4 text-left">Status</th>
                                            <th class="px-6 py-4 text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                        <template x-for="order in recentOrders" :key="order.id">
                                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/30 transition">
                                                <td class="px-6 py-3 font-mono text-xs font-bold text-gray-700 dark:text-gray-300" x-text="order.receipt_no"></td>
                                                <td class="px-6 py-3 text-gray-700 dark:text-gray-300" x-text="order.cashier"></td>
                                                <td class="px-6 py-3 text-gray-500 text-xs max-w-xs truncate" x-text="order.items.map(i => i.name + ' x' + i.quantity).join(', ')"></td>
                                                <td class="px-6 py-3 text-gray-500 text-xs" x-text="order.time"></td>
                                                <td class="px-6 py-3">
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 capitalize" x-text="order.status"></span>
                                                </td>
                                                <td class="px-6 py-3 text-right font-extrabold text-amber-600 dark:text-amber-400" x-text="'$' + order.total"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

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

                    {{-- ---- TAB: REPORTS ---- --}}
                    <div x-show="activeTab === 'reports'" x-cloak class="space-y-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Reports & Analytics</h1>
                            <p class="text-xs text-gray-500 mt-0.5">Performance insights based on real order data</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
                                <h3 class="font-bold text-gray-800 dark:text-white mb-5">Revenue Summary</h3>
                                <div class="space-y-4">
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-500 font-medium">Today's Revenue</span>
                                            <span class="font-bold text-gray-800 dark:text-white" x-text="'$' + todayRevenue">${{ number_format($todayRevenue, 2) }}</span>
                                        </div>
                                        <div class="w-full bg-gray-100 dark:bg-gray-800 h-3 rounded-full overflow-hidden">
                                            <div class="bg-green-500 h-full rounded-full transition-all" style="width: {{ $totalRevenue > 0 ? min(100, ($todayRevenue / max($totalRevenue, 1)) * 100) : 0 }}%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-500 font-medium">All-Time Revenue</span>
                                            <span class="font-bold text-gray-800 dark:text-white" x-text="'$' + totalRevenue">${{ number_format($totalRevenue, 2) }}</span>
                                        </div>
                                        <div class="w-full bg-gray-100 dark:bg-gray-800 h-3 rounded-full overflow-hidden">
                                            <div class="bg-purple-500 h-full rounded-full" style="width: 100%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col justify-between">
                                <h3 class="font-bold text-gray-800 dark:text-white mb-4">Order Totals</h3>
                                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                                    <div class="py-2.5 flex justify-between text-sm">
                                        <span class="text-gray-500">All-Time Orders</span>
                                        <span class="font-semibold text-gray-800 dark:text-white" x-text="totalOrders + ' orders'">{{ $totalOrders }} orders</span>
                                    </div>
                                    <div class="py-2.5 flex justify-between text-sm">
                                        <span class="text-gray-500">Today's Orders</span>
                                        <span class="font-semibold text-amber-600" x-text="todayOrders + ' orders'">{{ $todayOrders }} orders</span>
                                    </div>
                                    <div class="py-2.5 flex justify-between text-sm">
                                        <span class="text-gray-500">All-Time Revenue</span>
                                        <span class="font-semibold text-purple-600" x-text="'$' + totalRevenue">${{ number_format($totalRevenue, 2) }}</span>
                                    </div>
                                    <div class="py-2.5 flex justify-between text-sm">
                                        <span class="text-gray-500">Today's Revenue</span>
                                        <span class="font-semibold text-green-600" x-text="'$' + todayRevenue">${{ number_format($todayRevenue, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>

        @else
        {{-- ==================== EMPLOYEE POS VIEW ==================== --}}
        <div class="h-full flex flex-col overflow-hidden" x-data="{
            searchQuery: '',
            categoryFilter: 'all',
            cart: [],
            isSubmitting: false,
            showReceiptModal: false,
            errorMessage: '',
            lastOrderReceipt: null,
            products: [
                @foreach($allProducts as $product)
                {
                    id: {{ $product->id }},
                    name: '{{ addslashes($product->name) }}',
                    description: '{{ addslashes($product->description) }}',
                    price: {{ $product->price }},
                    category: '{{ $product->category }}',
                    image: '{{ $product->image_path ? Storage::url($product->image_path) : '' }}',
                },
                @endforeach
            ],
            addToCart(product) {
                let existing = this.cart.find(item => item.id === product.id);
                if (existing) {
                    existing.quantity++;
                } else {
                    this.cart.push({ id: product.id, name: product.name, price: product.price, quantity: 1 });
                }
            },
            removeFromCart(id) {
                this.cart = this.cart.filter(item => item.id !== id);
            },
            updateQuantity(id, change) {
                let item = this.cart.find(i => i.id === id);
                if (item) {
                    item.quantity += change;
                    if (item.quantity <= 0) this.removeFromCart(id);
                }
            },
            get itemCount() {
                return this.cart.reduce((sum, i) => sum + i.quantity, 0);
            },
            get subtotal() {
                return this.cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
            },
            get tax() { return this.subtotal * 0.10; },
            get total() { return this.subtotal + this.tax; },
            get filteredProducts() {
                return this.products.filter(p => {
                    const ms = p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                               p.description.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const mc = this.categoryFilter === 'all' || p.category === this.categoryFilter;
                    return ms && mc;
                });
            },
            async checkout() {
                if (this.cart.length === 0 || this.isSubmitting) return;
                this.isSubmitting = true;
                this.errorMessage = '';

                const payload = {
                    items: this.cart.map(i => ({ product_id: i.id, quantity: i.quantity }))
                };

                try {
                    const res = await fetch('{{ route('orders.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json();

                    if (res.ok && data.success) {
                        this.lastOrderReceipt = data;
                        this.cart = [];
                        this.showReceiptModal = true;
                    } else {
                        this.errorMessage = data.message || 'Something went wrong. Please try again.';
                    }
                } catch (e) {
                    this.errorMessage = 'Network error. Please check connection and retry.';
                } finally {
                    this.isSubmitting = false;
                }
            }
        }">

            {{-- POS Header --}}
            <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-6 py-4 shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="bg-amber-500 p-2 rounded-xl text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-800 dark:text-white">Coffee POS</h1>
                        <p class="text-xs text-gray-500">Cashier: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span></p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-3 py-1.5 rounded-xl font-medium hidden sm:inline-block">{{ now()->format('F d, Y') }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center space-x-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 dark:bg-red-950/20 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl text-xs font-semibold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Log Out</span>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Body --}}
            <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">

                {{-- Product catalog --}}
                <div class="flex-1 p-4 md:p-6 overflow-y-auto space-y-5">
                    {{-- Search & filters --}}
                    <div class="flex flex-col sm:flex-row gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800">
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

                    {{-- Product grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <div @click="addToCart(product)"
                                 class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden flex flex-col cursor-pointer hover:border-amber-500 hover:shadow-lg active:scale-95 transition-all select-none group">

                                {{-- Product photo --}}
                                <div class="relative h-36 w-full overflow-hidden bg-gray-100 dark:bg-gray-800 shrink-0">
                                    <template x-if="product.image">
                                        <img :src="product.image" :alt="product.name"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </template>
                                    <template x-if="!product.image">
                                        <div class="w-full h-full flex items-center justify-center"
                                             :class="product.category === 'milktea' ? 'bg-gradient-to-br from-blue-100 to-indigo-200 dark:from-blue-900/40 dark:to-indigo-900/40' : 'bg-gradient-to-br from-amber-100 to-orange-200 dark:from-amber-900/40 dark:to-orange-900/40'">
                                            <span class="text-4xl font-black opacity-25 text-gray-700" x-text="product.name.charAt(0)"></span>
                                        </div>
                                    </template>
                                    {{-- Category badge --}}
                                    <div class="absolute top-1.5 left-1.5">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase backdrop-blur-sm bg-white/80 dark:bg-gray-900/80"
                                              :class="product.category==='milktea' ? 'text-blue-700' : 'text-amber-700'"
                                              x-text="product.category==='milktea' ? 'Tea' : 'Snack'"></span>
                                    </div>
                                    {{-- Add indicator --}}
                                    <div class="absolute inset-0 bg-amber-500/0 group-hover:bg-amber-500/10 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <div class="bg-amber-500 text-white rounded-full w-9 h-9 flex items-center justify-center shadow-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Product info --}}
                                <div class="p-3 flex flex-col flex-1">
                                    <h3 class="font-bold text-xs text-gray-900 dark:text-white line-clamp-2 leading-tight" x-text="product.name"></h3>
                                    <p class="text-[10px] text-gray-400 line-clamp-1 mt-0.5" x-text="product.description"></p>
                                    <div class="mt-auto pt-2">
                                        <span class="text-sm font-extrabold text-amber-600 dark:text-amber-400" x-text="'$' + parseFloat(product.price).toFixed(2)"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Cart panel --}}
                <div class="w-full lg:w-96 bg-white dark:bg-gray-900 border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-gray-800 flex flex-col overflow-hidden shrink-0">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/50">
                        <span class="font-bold text-gray-800 dark:text-white">Current Order</span>
                        <span class="bg-amber-500 text-white text-xs px-2.5 py-1 rounded-full font-bold" x-text="itemCount + (itemCount === 1 ? ' item' : ' items')">0 items</span>
                    </div>

                    {{-- Error banner --}}
                    <div x-show="errorMessage" x-cloak class="mx-4 mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-xs text-red-700 dark:text-red-400 font-medium" x-text="errorMessage"></div>

                    {{-- Cart items --}}
                    <div class="flex-1 overflow-y-auto p-4 space-y-3">
                        <template x-if="cart.length === 0">
                            <div class="h-full flex flex-col items-center justify-center text-center p-6 space-y-2">
                                <div class="bg-amber-500/10 text-amber-500 p-4 rounded-full">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Cart is empty</p>
                                <p class="text-xs text-gray-400">Tap a product to add it to the order</p>
                            </div>
                        </template>

                        <template x-for="item in cart" :key="item.id">
                            <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-950 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                                <div class="flex-1 min-w-0 pr-2">
                                    <p class="text-xs font-bold text-gray-800 dark:text-white truncate" x-text="item.name"></p>
                                    <p class="text-[11px] text-amber-600 font-extrabold mt-0.5" x-text="'$' + parseFloat(item.price).toFixed(2)"></p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-1">
                                        <button @click="updateQuantity(item.id, -1)" class="w-5 h-5 flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded font-bold text-xs">-</button>
                                        <span class="w-6 text-center text-xs font-bold text-gray-800 dark:text-white" x-text="item.quantity"></span>
                                        <button @click="updateQuantity(item.id, 1)" class="w-5 h-5 flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded font-bold text-xs">+</button>
                                    </div>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 w-12 text-right" x-text="'$' + (item.price * item.quantity).toFixed(2)"></span>
                                    <button @click="removeFromCart(item.id)" class="text-red-400 hover:text-red-600 transition p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Totals & checkout --}}
                    <div class="p-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 space-y-2.5 shrink-0">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Subtotal</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="'$' + parseFloat(subtotal).toFixed(2)">$0.00</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Tax (10%)</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="'$' + parseFloat(tax).toFixed(2)">$0.00</span>
                        </div>
                        <div class="flex justify-between text-sm font-extrabold border-t border-dashed border-gray-200 dark:border-gray-800 pt-2.5 text-gray-800 dark:text-white">
                            <span>Total</span>
                            <span class="text-amber-600 dark:text-amber-400" x-text="'$' + parseFloat(total).toFixed(2)">$0.00</span>
                        </div>

                        <button @click="checkout"
                                :disabled="cart.length === 0 || isSubmitting"
                                :class="cart.length === 0 || isSubmitting ? 'bg-gray-300 dark:bg-gray-800 text-gray-400 cursor-not-allowed' : 'bg-amber-500 hover:bg-amber-600 text-white shadow-lg shadow-amber-500/20 active:scale-[0.98]'"
                                class="w-full mt-1 font-bold py-3 rounded-xl text-sm transition-all flex items-center justify-center space-x-2">
                            <template x-if="!isSubmitting">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <span>Place Order & Print</span>
                                </span>
                            </template>
                            <template x-if="isSubmitting">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    <span>Processing...</span>
                                </span>
                            </template>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Receipt Modal --}}
            <div x-show="showReceiptModal" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-60">
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 max-w-sm w-full shadow-2xl" @click.away="showReceiptModal = false">
                    <div class="text-center pb-4 border-b border-dashed border-gray-200 dark:border-gray-700">
                        <div class="w-12 h-12 rounded-full bg-green-500/10 text-green-500 flex items-center justify-center mx-auto mb-2.5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="font-extrabold text-gray-800 dark:text-white text-base">Order Placed!</h3>
                        <p class="text-[10px] text-gray-400 mt-0.5">Coffee Shop POS — Official Receipt</p>
                    </div>

                    <div class="py-4 space-y-2 text-xs text-gray-600 dark:text-gray-300">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Receipt No</span>
                            <span class="font-bold text-gray-800 dark:text-white font-mono" x-text="lastOrderReceipt?.receipt_no"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Cashier</span>
                            <span class="font-medium">{{ Auth::user()->name }}</span>
                        </div>

                        <div class="border-t border-dashed border-gray-200 dark:border-gray-700 pt-3 mt-2 space-y-1.5">
                            <template x-for="item in lastOrderReceipt?.items" :key="item.name">
                                <div class="flex justify-between">
                                    <span x-text="item.name + ' × ' + item.quantity"></span>
                                    <span class="font-semibold text-gray-800 dark:text-white" x-text="'$' + item.subtotal"></span>
                                </div>
                            </template>
                        </div>

                        <div class="border-t border-dashed border-gray-200 dark:border-gray-700 pt-3 space-y-1">
                            <div class="flex justify-between text-gray-400">
                                <span>Subtotal</span>
                                <span x-text="'$' + lastOrderReceipt?.subtotal"></span>
                            </div>
                            <div class="flex justify-between text-gray-400">
                                <span>Tax (10%)</span>
                                <span x-text="'$' + lastOrderReceipt?.tax"></span>
                            </div>
                            <div class="flex justify-between font-extrabold text-sm text-gray-800 dark:text-white pt-1">
                                <span>Total Paid</span>
                                <span class="text-amber-600 dark:text-amber-400" x-text="'$' + lastOrderReceipt?.total"></span>
                            </div>
                        </div>
                    </div>

                    <button @click="showReceiptModal = false"
                            class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 rounded-xl text-sm transition shadow-lg shadow-amber-500/10">
                        Start New Order
                    </button>
                </div>
            </div>
        </div>
        @endif
    </body>
</html>
