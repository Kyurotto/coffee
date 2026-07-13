{{-- ---- TAB: REPORTS ---- --}}
<div x-show="activeTab === 'reports'" x-cloak class="space-y-6">
    <div class="flex justify-between items-start">
        <div class="print:hidden">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Reports & Analytics</h1>
            <p class="text-xs text-gray-500 mt-0.5">Performance insights based on real order data</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center gap-2 print:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 2v-4a2 2 0 012-2h2a2 2 0 012 2v4M5 1h14a2 2 0 012 2v16a2 2 0 01-2 2H5a2 2 0 01-2-2V3a2 2 0 012-2z" />
            </svg>
            Print Report
        </button>
    </div>
    <livewire:report-summary />
</div>
