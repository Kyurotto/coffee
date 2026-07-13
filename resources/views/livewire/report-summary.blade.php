<div x-data class="space-y-6">
    <style>
        @media print {
            body {
                background-color: white !important;
                color: black !important;
            }
            .dark\:bg-gray-900, .dark\:bg-gray-800, .dark\:text-white, .dark\:text-gray-300, .dark\:border-gray-800, .dark\:border-gray-700 {
                background-color: white !important;
                color: black !important;
                border-color: #e5e7eb !important;
            }
            .shadow-sm {
                box-shadow: none !important;
            }
            .border {
                border-color: #e5e7eb !important;
            }
            canvas {
                max-width: 100% !important;
                height: auto !important;
            }
        }
    </style>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex flex-wrap gap-4 items-end print:hidden">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Month</label>
            <select wire:model.live="selectedMonth" class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 pr-8">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected($m == $selectedMonth)>{{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Year</label>
            <select wire:model.live="selectedYear" class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 pr-8">
                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" @selected($y == $selectedYear)>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <h3 class="font-bold text-gray-800 dark:text-white mb-5">Revenue Summary</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500 font-medium">Today's Revenue</span>
                        <span class="font-bold text-gray-800 dark:text-white">${{ number_format($todayRevenue, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-800 h-3 rounded-full overflow-hidden">
                        <div class="bg-green-500 h-full rounded-full transition-all" style="width: {{ $totalRevenue > 0 ? min(100, ($todayRevenue / max($totalRevenue, 1)) * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500 font-medium">Period Revenue</span>
                        <span class="font-bold text-gray-800 dark:text-white">${{ number_format($totalRevenue, 2) }}</span>
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
                    <span class="text-gray-500">Period Orders</span>
                    <span class="font-semibold text-gray-800 dark:text-white">{{ $totalOrders }} orders</span>
                </div>
                <div class="py-2.5 flex justify-between text-sm">
                    <span class="text-gray-500">Today's Orders</span>
                    <span class="font-semibold text-amber-600">{{ $todayOrders }} orders</span>
                </div>
                <div class="py-2.5 flex justify-between text-sm">
                    <span class="text-gray-500">Period Revenue</span>
                    <span class="font-semibold text-purple-600">${{ number_format($totalRevenue, 2) }}</span>
                </div>
                <div class="py-2.5 flex justify-between text-sm">
                    <span class="text-gray-500">Today's Revenue</span>
                    <span class="font-semibold text-green-600">${{ number_format($todayRevenue, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <h3 class="font-bold text-gray-800 dark:text-white mb-5">Revenue by Category</h3>
            <div wire:ignore class="relative h-64">
                <canvas id="revenuePieChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Pass updated chart data to JS via a hidden element updated on every render --}}
    <div id="chartData" data-chart='@json($categoryRevenue)' style="display:none;"></div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        (function () {
            Chart.register(ChartDataLabels);

            var colors = [
                '#10B981', '#8B5CF6', '#F59E0B', '#3B82F6', '#EF4444',
                '#6366F1', '#EC4899', '#14B8A6', '#F97316', '#A855F7'
            ];

            function buildChart() {
                var canvas = document.getElementById('revenuePieChart');
                if (!canvas) return;

                // Destroy old instance safely
                if (window._revenuePieChartInstance) {
                    window._revenuePieChartInstance.destroy();
                    window._revenuePieChartInstance = null;
                }

                var raw = document.getElementById('chartData');
                var data = raw ? JSON.parse(raw.getAttribute('data-chart')) : {};

                window._revenuePieChartInstance = new Chart(canvas.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: Object.keys(data),
                        datasets: [{
                            data: Object.values(data),
                            backgroundColor: colors,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#666'
                                }
                            },
                            datalabels: {
                                formatter: (value, ctx) => {
                                    let sum = 0;
                                    let dataArr = ctx.chart.data.datasets[0].data;
                                    dataArr.map(data => {
                                        sum += data;
                                    });
                                    let percentage = (value * 100 / sum).toFixed(1) + "%";
                                    return percentage;
                                },
                                color: '#fff',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            }
                        }
                    }
                });
            }

            function refreshChart() {
                var canvas = document.getElementById('revenuePieChart');
                if (!canvas) return;

                var raw = document.getElementById('chartData');
                var data = raw ? JSON.parse(raw.getAttribute('data-chart')) : {};

                if (window._revenuePieChartInstance) {
                    window._revenuePieChartInstance.data.labels = Object.keys(data);
                    window._revenuePieChartInstance.data.datasets[0].data = Object.values(data);
                    window._revenuePieChartInstance.update();
                } else {
                    buildChart();
                }
            }

            // Build on first load
            document.addEventListener('DOMContentLoaded', buildChart);

            // After every Livewire component update, refresh the chart using the new data attribute
            document.addEventListener('livewire:update', refreshChart);
        })();
    </script>
</div>
