<div wire:key="profit-by-category" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('reports.profit_by_category.title') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('reports.profit_by_category.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-native-select
                wire:model.live="period"
                :options="[
                    ['name' => __('reports.periods.today'), 'id' => 'today'],
                    ['name' => __('reports.periods.yesterday'), 'id' => 'yesterday'],
                    ['name' => __('reports.periods.last_7_days'), 'id' => 'last_7_days'],
                    ['name' => __('reports.periods.last_week'), 'id' => 'last_week'],
                    ['name' => __('reports.periods.last_30_days'), 'id' => 'last_30_days'],
                    ['name' => __('reports.periods.this_month'), 'id' => 'this_month'],
                    ['name' => __('reports.periods.last_month'), 'id' => 'last_month'],
                    ['name' => __('reports.periods.last_6_months'), 'id' => 'last_6_months'],
                    ['name' => __('reports.periods.this_year'), 'id' => 'this_year'],
                    ['name' => __('reports.periods.last_year'), 'id' => 'last_year'],
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
            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('reports.profit_by_category.revenue') }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('reports.profit_by_category.profit') }}</span>
        </div>
    </div>

    <div
        wire:ignore
        id="chart-profit-by-category"
        x-data="{
            labels: @entangle('chartDataArray.labels'),
            revenue: @entangle('chartDataArray.revenue'),
            profit: @entangle('chartDataArray.profit'),
            chart: null,
            init() {
                this.$nextTick(() => {
                    this.initChart();
                });

                this.$watch('revenue', () => this.updateChart());
                this.$watch('profit', () => this.updateChart());
                this.$watch('labels', () => this.updateChart());

                const observer = new MutationObserver(() => {
                    if (this.chart) {
                        this.chart.updateOptions({
                            tooltip: {
                                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                            }
                        });
                    }
                });
                observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            },
            updateChart() {
                if (this.chart) {
                    this.chart.updateOptions({
                        xaxis: { categories: this.labels },
                        series: [
                            { name: '{{ __('reports.profit_by_category.revenue') }}', data: this.revenue },
                            { name: '{{ __('reports.profit_by_category.profit') }}', data: this.profit }
                        ]
                    });
                }
            },
            initChart() {
                if (!this.$refs.chart) {
                    setTimeout(() => this.initChart(), 50);
                    return;
                }

                if (this.chart) {
                    this.chart.destroy();
                }

                this.chart = new ApexCharts(this.$refs.chart, {
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
                            name: '{{ __('reports.profit_by_category.revenue') }}',
                            data: this.revenue
                        },
                        {
                            name: '{{ __('reports.profit_by_category.profit') }}',
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
                                fontSize: '11px',
                                fontFamily: 'Inter, ui-sans-serif, system-ui'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#9ca3af',
                                fontSize: '11px',
                                fontFamily: 'Inter, ui-sans-serif, system-ui'
                            },
                            formatter: function(val) {
                                return 'R$ ' + val.toLocaleString('pt-BR');
                            }
                        }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                        y: {
                            formatter: function(val) {
                                return 'R$ ' + val.toLocaleString('pt-BR');
                            }
                        }
                    },
                    legend: { show: false }
                });
                this.chart.render();
            }
        }"
        class="w-full"
    >
        <div x-ref="chart"></div>
    </div>
</div>
