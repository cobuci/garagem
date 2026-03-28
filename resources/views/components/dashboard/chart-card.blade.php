@props(['chartData'])

<div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('dashboard.sales_overview') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('dashboard.last_6_months') }}</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.sales') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.profit') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.not_paid') }}</span>
            </div>
        </div>
    </div>

    <div
        x-data="{
            labels: @js($chartData['labels']),
            sales: @js($chartData['sales']),
            profit: @js($chartData['profit']),
            pending: @js($chartData['pending']),
            init() {
                let chart = new ApexCharts(this.$refs.chart, {
                    chart: {
                        type: 'area',
                        height: 350,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        fontFamily: 'Inter, ui-sans-serif, system-ui',
                        background: 'transparent'
                    },
                    series: [
                        {
                            name: '{{ __('dashboard.sales') }}',
                            data: this.sales
                        },
                        {
                            name: '{{ __('dashboard.profit') }}',
                            data: this.profit
                        },
                        {
                            name: '{{ __('dashboard.not_paid') }}',
                            data: this.pending
                        }
                    ],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [20, 100, 100, 100]
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        curve: 'smooth',
                        width: [3, 3, 2],
                        colors: ['#6366f1', '#10b981', '#f59e0b'],
                        dashArray: [0, 0, 5]
                    },
                    colors: ['#6366f1', '#10b981', '#f59e0b'],
                    grid: {
                        borderColor: 'rgba(156, 163, 175, 0.1)',
                        strokeDashArray: 4,
                        padding: { left: 10, right: 10, top: 0, bottom: 0 }
                    },
                    xaxis: {
                        categories: this.labels,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: {
                            style: {
                                colors: '#9ca3af',
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#9ca3af',
                                fontSize: '12px'
                            },
                            formatter: function(val) {
                                return 'R$ ' + val.toLocaleString('pt-BR');
                            }
                        }
                    },
                    tooltip: {
                        x: { show: true },
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                    },
                    legend: { show: false }
                });
                chart.render();
            }
        }"
        class="w-full"
    >
        <div x-ref="chart"></div>
    </div>
</div>
