@props(['chartData'])

<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('dashboard.sales_overview') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('dashboard.last_6_months') }}</p>
        </div>
        <div class="flex items-center flex-wrap gap-x-4 gap-y-2">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.total') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky-600"></span>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.paid_sales') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.profit') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.not_paid') }}</span>
            </div>
        </div>
    </div>

    <div
        wire:ignore
        x-data="{
            labels: @js($chartData['labels']),
            sales: @js($chartData['sales']),
            profit: @js($chartData['profit']),
            pending: @js($chartData['pending']),
            total: @js($chartData['total']),
            init() {
                let chart = new ApexCharts(this.$refs.chart, {
                    chart: {
                        type: 'area',
                        height: 350,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
                        background: 'transparent'
                    },
                    series: [
                        {
                            name: '{{ __('dashboard.total') }}',
                            data: this.total
                        },
                        {
                            name: '{{ __('dashboard.paid_sales') }}',
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
                            opacityFrom: 0.35,
                            opacityTo: 0.05,
                            stops: [20, 100, 100, 100]
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        curve: 'smooth',
                        width: [3, 2, 2.5, 2],
                        colors: ['#4f46e5', '#0284c7', '#10b981', '#f59e0b'],
                        dashArray: [0, 0, 0, 4]
                    },
                    colors: ['#4f46e5', '#0284c7', '#10b981', '#f59e0b'],
                    grid: {
                        borderColor: 'rgba(156, 163, 175, 0.12)',
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
                                fontSize: '12px',
                                fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#9ca3af',
                                fontSize: '12px',
                                fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                            },
                            formatter: function(val) {
                                return 'R$ ' + val.toLocaleString('pt-BR');
                            }
                        }
                    },
                    tooltip: {
                        x: { show: true },
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                        y: {
                            formatter: function(val) {
                                return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
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
