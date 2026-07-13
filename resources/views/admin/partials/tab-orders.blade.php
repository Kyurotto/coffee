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

    {{-- Summary bar + orders table --}}
    {{-- Auto-refreshes every 15 seconds via Livewire wire:poll --}}
    <livewire:order-history />
</div>
