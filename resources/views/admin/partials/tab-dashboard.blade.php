{{-- ---- TAB: DASHBOARD ---- --}}
<div x-show="activeTab === 'dashboard'" x-cloak class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Overview</h1>
            <p class="text-xs text-gray-500 mt-0.5">Live stats — refreshes every 15 seconds</p>
        </div>
        <span class="text-xs text-gray-500 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 px-3 py-1.5 rounded-xl font-medium">{{ now()->format('F d, Y') }}</span>
    </div>

    {{-- Stat cards + recent orders + quick summary --}}
    {{-- Auto-refreshes every 15 seconds via Livewire wire:poll --}}
    <livewire:dashboard-stats />
</div>
