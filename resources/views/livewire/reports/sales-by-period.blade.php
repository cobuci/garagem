<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('reports.sales_by_period') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('reports.sales_and_profit_overview') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-native-select
                wire:model.live="period"
                :options="[
                    ['name' => __('reports.periods.today'), 'id' => 'today'],
                    ['name' => __('reports.periods.last_7_days'), 'id' => 'last_7_days'],
                    ['name' => __('reports.periods.last_30_days'), 'id' => 'last_30_days'],
                    ['name' => __('reports.periods.this_month'), 'id' => 'this_month'],
                    ['name' => __('reports.periods.last_6_months'), 'id' => 'last_6_months'],
                ]"
                option-label="name"
                option-value="id"
                class="w-full sm:w-48"
            />
        </div>
    </div>

    <div class="grid grid-cols-2 sm:flex sm:items-center gap-4 mb-6">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('reports.sales') }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('reports.profit') }}</span>
        </div>
    </div>

    <div
        x-data="{
            labels: @js($this->chartData['labels']),
            sales: @js($this->chartData['sales']),
            profit: @js($this->chartData['profit']),
            init() {
                let chart = new ApexCharts(this.$refs.chart, {
                    chart: {
                        type: 'area',
                        height: 350,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        fontFamily: 'Inter, ui-sans-serif, system-ui',
                        background: 'transparent',
                        animations: { enabled: true }
                    },
                    series: [
                        {
                            name: '{{ __('reports.sales') }}',
                            data: this.sales
                        },
                        {
                            name: '{{ __('reports.profit') }}',
                            data: this.profit
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
                        width: 3,
                        colors: ['#6366f1', '#10b981']
                    },
                    colors: ['#6366f1', '#10b981'],
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

                this.$watch('sales', (value) => {
                    chart.updateSeries([
                        { name: '{{ __('reports.sales') }}', data: value },
                        { name: '{{ __('reports.profit') }}', data: this.profit }
                    ]);
                });

                this.$watch('labels', (value) => {
                    chart.updateOptions({
                        xaxis: { categories: value }
                    });
                });
            }
        }"
        class="w-full"
    >
        <div x-ref="chart"></div>
    </div>
</div>
