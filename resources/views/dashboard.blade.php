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
        <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

        @livewireStyles

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

            // Live order stats — start with server-rendered values;
            // updated by Livewire's stats-refreshed browser event
            totalOrders:  {{ $totalOrders ?? 0 }},
            totalRevenue: '{{ number_format($totalRevenue ?? 0, 2) }}',
            todayRevenue: '{{ number_format($todayRevenue ?? 0, 2) }}',
            todayOrders:  {{ $todayOrders ?? 0 }},

            stocks: {
                @foreach($allProducts as $product)
                    '{{ $product->id }}': {{ $product->id % 3 == 0 ? rand(1, 5) : ($product->id % 5 == 0 ? 0 : rand(15, 50)) }},
                @endforeach
            },

            adjustStock(id, amount) {
                this.stocks[id] = Math.max(0, this.stocks[id] + amount);
            },

            // Livewire broadcasts stats-refreshed after each poll so the sidebar
            // badge and Reports tab stay in sync without a separate fetch call
            init() {
                window.addEventListener('stats-refreshed', (e) => {
                    const d = e.detail || {};
                    if (d.totalOrders  !== undefined) this.totalOrders  = d.totalOrders;
                    if (d.totalRevenue !== undefined) this.totalRevenue = d.totalRevenue;
                    if (d.todayRevenue !== undefined) this.todayRevenue = d.todayRevenue;
                    if (d.todayOrders  !== undefined) this.todayOrders  = d.todayOrders;
                });
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
                        is_new: {{ $product->created_at->gt(now()->subHours(24)) ? 'true' : 'false' }},
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

                <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50 flex flex-col space-y-3"
                     x-data="{
                        showProfile: false,
                        saving: false,
                        success: '',
                        errors: {},
                        form: {
                            name: '{{ addslashes(Auth::user()->name) }}',
                            email: '{{ addslashes(Auth::user()->email) }}',
                            current_password: '',
                            new_password: '',
                            new_password_confirmation: ''
                        },
                        displayName: '{{ addslashes(Auth::user()->name) }}',
                        displayEmail: '{{ addslashes(Auth::user()->email) }}',
                        displayInitial: '{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}',
                        open() {
                            this.form.name = this.displayName;
                            this.form.email = this.displayEmail;
                            this.form.current_password = '';
                            this.form.new_password = '';
                            this.form.new_password_confirmation = '';
                            this.errors = {};
                            this.success = '';
                            this.showProfile = true;
                        },
                        async save() {
                            this.saving = true;
                            this.errors = {};
                            this.success = '';
                            try {
                                const res = await fetch('{{ route('profile.ajax-update') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify(this.form)
                                });
                                const data = await res.json();
                                if (!res.ok) {
                                    this.errors = data.errors || {};
                                } else {
                                    this.displayName    = data.name;
                                    this.displayEmail   = data.email;
                                    this.displayInitial = data.initial;
                                    this.form.current_password = '';
                                    this.form.new_password = '';
                                    this.form.new_password_confirmation = '';
                                    this.success = 'Profile updated successfully!';
                                }
                            } catch(e) {
                                this.errors = { name: ['Something went wrong. Please try again.'] };
                            }
                            this.saving = false;
                        }
                     }">

                    {{-- Clickable user card --}}
                    <button type="button" @click="open()"
                        class="flex items-center space-x-3 px-2 w-full rounded-xl py-2 hover:bg-amber-50 dark:hover:bg-amber-900/10 transition-all group cursor-pointer text-left">
                        <div class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold text-sm shrink-0 group-hover:bg-amber-500 group-hover:text-white transition-colors"
                             x-text="displayInitial"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-800 dark:text-white truncate" x-text="displayName"></p>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate" x-text="displayEmail"></p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-amber-500 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>

                    {{-- Profile Edit Modal --}}
                    <template x-teleport="body">
                        <div x-show="showProfile" x-cloak
                             class="fixed inset-0 z-50 flex items-center justify-center p-4"
                             @keydown.escape.window="showProfile = false">

                            {{-- Backdrop --}}
                            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showProfile = false"></div>

                            {{-- Panel --}}
                            <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
                                 @click.stop>

                                {{-- Header --}}
                                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold text-base"
                                             x-text="displayInitial"></div>
                                        <div>
                                            <h2 class="text-base font-bold text-gray-900 dark:text-white">Edit Profile</h2>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Update your account information</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="showProfile = false"
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors rounded-lg p-1 hover:bg-gray-100 dark:hover:bg-gray-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                {{-- Success banner --}}
                                <div x-show="success" x-cloak
                                     class="mx-6 mt-4 flex items-center space-x-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 rounded-xl px-4 py-3 text-sm">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span x-text="success"></span>
                                </div>

                                {{-- Form --}}
                                <div class="px-6 py-5 space-y-4">

                                    {{-- Name --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Full Name</label>
                                        <input type="text" x-model="form.name" placeholder="Your name"
                                            :class="errors.name ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-700'"
                                            class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                                        <p x-show="errors.name" x-text="errors.name && errors.name[0]" class="mt-1 text-xs text-red-500"></p>
                                    </div>

                                    {{-- Email --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                                        <input type="email" x-model="form.email" placeholder="you@example.com"
                                            :class="errors.email ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-700'"
                                            class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                                        <p x-show="errors.email" x-text="errors.email && errors.email[0]" class="mt-1 text-xs text-red-500"></p>
                                    </div>

                                    {{-- Divider --}}
                                    <div class="relative py-1">
                                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100 dark:border-gray-800"></div></div>
                                        <div class="relative flex justify-center">
                                            <span class="bg-white dark:bg-gray-900 px-3 text-[10px] font-semibold uppercase tracking-wider text-gray-400">Change Password <span class="font-normal normal-case">(optional)</span></span>
                                        </div>
                                    </div>

                                    {{-- Current Password --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Current Password</label>
                                        <input type="password" x-model="form.current_password" placeholder="Enter current password" autocomplete="current-password"
                                            :class="errors.current_password ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-700'"
                                            class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                                        <p x-show="errors.current_password" x-text="errors.current_password && errors.current_password[0]" class="mt-1 text-xs text-red-500"></p>
                                    </div>

                                    {{-- New Password --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">New Password</label>
                                        <input type="password" x-model="form.new_password" placeholder="Min. 8 characters" autocomplete="new-password"
                                            :class="errors.new_password ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-700'"
                                            class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                                        <p x-show="errors.new_password" x-text="errors.new_password && errors.new_password[0]" class="mt-1 text-xs text-red-500"></p>
                                    </div>

                                    {{-- Confirm New Password --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Confirm New Password</label>
                                        <input type="password" x-model="form.new_password_confirmation" placeholder="Repeat new password" autocomplete="new-password"
                                            :class="errors.new_password_confirmation ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-700'"
                                            class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                                        <p x-show="errors.new_password_confirmation" x-text="errors.new_password_confirmation && errors.new_password_confirmation[0]" class="mt-1 text-xs text-red-500"></p>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex items-center space-x-3 pt-1">
                                        <button type="button" @click="showProfile = false"
                                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition-all">
                                            Cancel
                                        </button>
                                        <button type="button" @click="save()" :disabled="saving"
                                            :class="saving ? 'opacity-70 cursor-not-allowed' : ''"
                                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl shadow-lg shadow-amber-500/25 transition-all flex items-center justify-center space-x-2">
                                            <template x-if="!saving">
                                                <span>Save Changes</span>
                                            </template>
                                            <template x-if="saving">
                                                <span class="flex items-center space-x-2">
                                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                    </svg>
                                                    <span>Saving…</span>
                                                </span>
                                            </template>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

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

                    @include('admin.partials.tab-dashboard')

                    @include('admin.partials.tab-orders')

                    @include('admin.partials.tab-products')

                    @include('admin.partials.tab-stocks')

                    @include('admin.partials.tab-reports')

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
            showPaymentModal: false,
            selectedPayment: '',
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
                    is_new: {{ $product->created_at->gt(now()->subHours(24)) ? 'true' : 'false' }},
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
                if (this.cart.length === 0 || this.isSubmitting || !this.selectedPayment) return;
                this.isSubmitting = true;
                this.errorMessage = '';
                this.showPaymentModal = false;

                const payload = {
                    items: this.cart.map(i => ({ product_id: i.id, quantity: i.quantity })),
                    payment_method: this.selectedPayment
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
                        this.selectedPayment = '';
                        this.showReceiptModal = true;
                    } else {
                        this.errorMessage = data.message || 'Something went wrong. Please try again.';
                    }
                } catch (e) {
                    this.errorMessage = 'Network error. Please check connection and retry.';
                } finally {
                    this.isSubmitting = false;
                }
            },
            paymentQRData: {
                online: 'https://pay.coffeeshop.com/checkout',
            },
            init() {
                this.$watch('selectedPayment', (val) => {
                    if (val && val !== 'physical') {
                        this.$nextTick(() => this.generateQR(val));
                    }
                });
                // Poll for new/updated products every 30 seconds
                this.pollProducts();
                setInterval(() => this.pollProducts(), 30000);
            },
            async pollProducts() {
                try {
                    const res = await fetch('{{ route('products.index') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    if (!data.products) return;
                    // Merge: add new products, update existing ones
                    data.products.forEach(p => {
                        const idx = this.products.findIndex(x => x.id === p.id);
                        if (idx === -1) {
                            // Newly added product — prepend it
                            this.products.unshift(p);
                        } else {
                            // Update existing (name/price/image may have changed)
                            this.products[idx] = p;
                        }
                    });
                    // Remove deleted products
                    const ids = data.products.map(p => p.id);
                    this.products = this.products.filter(p => ids.includes(p.id));
                } catch(e) { /* silent — no network disruption to UX */ }
            },
            generateQR(method) {
                const el = document.getElementById('qr-payment-box');
                if (!el) return;
                el.innerHTML = '';
                const data = this.paymentQRData[method];
                if (!data) return;
                const qrText = data + '?amount=' + parseFloat(this.total).toFixed(2);
                new QRCode(el, {
                    text: qrText,
                    width: 180,
                    height: 180,
                    colorDark: '#1f2937',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H,
                });
            },
            paymentLabel(method) {
                const labels = {
                    physical: 'Cash Payment',
                    online:   'Online Payment',
                };
                return labels[method] || method;
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
                                    {{-- NEW badge (pulsing) --}}
                                    <template x-if="product.is_new">
                                        <div class="absolute top-1.5 right-1.5">
                                            <span class="relative inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-green-500 text-white shadow">
                                                <span class="absolute inset-0 rounded animate-ping bg-green-400 opacity-60"></span>
                                                <span class="relative">NEW</span>
                                            </span>
                                        </div>
                                    </template>
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

                        <button @click="if (cart.length > 0 && !isSubmitting) { showPaymentModal = true; }"
                                :disabled="cart.length === 0 || isSubmitting"
                                :class="cart.length === 0 || isSubmitting ? 'bg-gray-300 dark:bg-gray-800 text-gray-400 cursor-not-allowed' : 'bg-amber-500 hover:bg-amber-600 text-white shadow-lg shadow-amber-500/20 active:scale-[0.98]'"
                                class="w-full mt-1 font-bold py-3 rounded-xl text-sm transition-all flex items-center justify-center space-x-2">
                            <template x-if="!isSubmitting">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
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

            {{-- Payment Method Modal --}}
            <div x-show="showPaymentModal" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-60">
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 max-w-sm w-full shadow-2xl max-h-[90vh] overflow-y-auto" @click.away="showPaymentModal = false">

                    {{-- Header --}}
                    <div class="text-center pb-4 border-b border-dashed border-gray-200 dark:border-gray-700 mb-5">
                        <div class="w-12 h-12 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center mx-auto mb-2.5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3-3V8a3 3 0 003 3z"/></svg>
                        </div>
                        <h3 class="font-extrabold text-gray-800 dark:text-white text-base">Select Payment Method</h3>
                        <p class="text-[10px] text-gray-400 mt-0.5">Choose how the customer will pay</p>
                    </div>

                    {{-- Payment Options --}}
                    <div class="space-y-3 mb-5">

                        {{-- Section: Physical --}}
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Physical Payment</p>
                        <button @click="selectedPayment = 'physical'"
                                :class="selectedPayment === 'physical' ? 'ring-2 ring-amber-500 bg-amber-50 dark:bg-amber-900/20 border-amber-400' : 'border-gray-200 dark:border-gray-700 hover:border-amber-400'"
                                class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl border transition-all text-left">
                            <div class="w-9 h-9 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-800 dark:text-white">Cash</p>
                                <p class="text-[10px] text-gray-400">Physical cash payment</p>
                            </div>
                            <div x-show="selectedPayment === 'physical'" class="w-5 h-5 rounded-full bg-amber-500 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </button>

                        {{-- Section: Online --}}
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mt-4 mb-1">Online / E-Wallet</p>

                        {{-- Online Payment (QR Code) --}}
                        <button @click="selectedPayment = 'online'"
                                :class="selectedPayment === 'online' ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/20 border-blue-400' : 'border-gray-200 dark:border-gray-700 hover:border-blue-400'"
                                class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl border transition-all text-left">
                            <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-800 dark:text-white">Online Payment</p>
                                <p class="text-[10px] text-gray-400">GCash, Maya, Maribank &amp; more — scan QR</p>
                            </div>
                            <div x-show="selectedPayment === 'online'" class="w-5 h-5 rounded-full bg-blue-500 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </button>

                    </div>

                    {{-- QR Code Panel (online only) --}}
                    <div x-show="selectedPayment === 'online'"
                         x-effect="if (selectedPayment === 'online') { $nextTick(() => generateQR(selectedPayment)); }"
                         class="mb-5 rounded-2xl border border-dashed border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-950/30 p-4 text-center">

                        {{-- Header --}}
                        <div class="flex items-center justify-center space-x-2 mb-3">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest">Scan QR to Pay</span>
                        </div>

                        {{-- QR code rendered here by qrcodejs --}}
                        <div id="qr-payment-box" class="flex justify-center items-center min-h-[184px]"></div>

                        {{-- Amount label --}}
                        <p class="mt-3 text-xs text-gray-400">
                            Amount to pay:
                            <span class="font-extrabold text-amber-600 dark:text-amber-400"
                                  x-text="'$' + parseFloat(total).toFixed(2)">$0.00</span>
                        </p>
                        <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-1">Show QR to customer — click Confirm after payment</p>
                    </div>

                    {{-- Order total reminder --}}
                    <div class="flex justify-between items-center text-sm font-bold border-t border-dashed border-gray-200 dark:border-gray-700 pt-4 mb-4">
                        <span class="text-gray-500">Total to Collect</span>
                        <span class="text-amber-600 dark:text-amber-400 text-base" x-text="'$' + parseFloat(total).toFixed(2)">$0.00</span>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex space-x-3">
                        <button @click="showPaymentModal = false; selectedPayment = ''"
                                class="flex-1 py-3 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            Cancel
                        </button>
                        <button @click="checkout()"
                                :disabled="!selectedPayment"
                                :class="!selectedPayment ? 'bg-gray-300 dark:bg-gray-800 text-gray-400 cursor-not-allowed' : 'bg-amber-500 hover:bg-amber-600 text-white shadow-lg shadow-amber-500/20 active:scale-[0.98]'"
                                class="flex-1 py-3 rounded-xl text-sm font-bold transition-all flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Confirm & Print</span>
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
                        <div class="flex justify-between">
                            <span class="text-gray-400">Payment</span>
                            <span class="font-bold capitalize"
                                  :class="{
                                      'text-green-600 dark:text-green-400': lastOrderReceipt?.payment_method === 'physical',
                                      'text-blue-600 dark:text-blue-400':   lastOrderReceipt?.payment_method === 'online',
                                  }"
                                  x-text="paymentLabel(lastOrderReceipt?.payment_method)"></span>
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
        @livewireScripts
    </body>
</html>
