{{-- ---- TAB: REPORTS ---- --}}
<div x-show="activeTab === 'reports'" x-cloak class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Reports & Analytics</h1>
        <p class="text-xs text-gray-500 mt-0.5">Performance insights based on real order data</p>
    </div>
    <livewire:report-summary />
</div>
