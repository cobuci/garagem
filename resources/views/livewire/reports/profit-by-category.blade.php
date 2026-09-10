<div wire:key="profit-by-category" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('reports.profit_by_category.title') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('reports.profit_by_category.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-native-select
                wire:model.live="period"
                noscroll
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
        wire:loading.delay.class="opacity-60 pointer-events-none transition-opacity duration-200"
        x-data="{
            labels: @entangle('chartDataArray.labels'),
            revenue: @entangle('chartDataArray.revenue'),
            profit: @entangle('chartDataArray.profit'),
            chart: null,
            observer: null,
            init() {
                this.$nextTick(() => {
                    this.initChart();
                });

                this.$watch('revenue', () => this.updateChart());
                this.$watch('profit', () => this.updateChart());
                this.$watch('labels', () => this.updateChart());

                this.observer = new MutationObserver(() => {
                    if (this.chart) {
                        const dark = document.documentElement.classList.contains('dark');
                        this.chart.updateOptions({
                            tooltip: {
                                theme: dark ? 'dark' : 'light'
                            },
                            grid: {
                                borderColor: dark ? 'rgba(156, 163, 175, 0.15)' : 'rgba(156, 163, 175, 0.1)'
                            }
                        });
                    }
                });
                this.observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            },
            destroy() {
                if (this.observer) this.observer.disconnect();
                if (this.chart) this.chart.destroy();
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

                const dark = document.documentElement.classList.contains('dark');
                this.chart = new ApexCharts(this.$refs.chart, {
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        fontFamily: 'Instrument Sans, sans-serif',
                        background: 'transparent',
                        animations: { enabled: true }
                    },
                    noData: {
                        text: '{{ __('reports.heatmap.no_data') }}',
                        align: 'center',
                        verticalAlign: 'middle',
                        style: {
                            color: '#9ca3af',
                            fontSize: '14px',
                            fontFamily: 'Instrument Sans, sans-serif'
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            borderRadius: 4,
                            endingShape: 'rounded'
                        },
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
                    dataLabels: { enabled: false },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    colors: ['#6366f1', '#10b981'],
                    grid: {
                        borderColor: dark ? 'rgba(156, 163, 175, 0.15)' : 'rgba(156, 163, 175, 0.1)',
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
                                fontFamily: 'Instrument Sans, sans-serif'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#9ca3af',
                                fontSize: '12px',
                                fontFamily: 'Instrument Sans, sans-serif'
                            },
                            formatter: function(val) {
                                return 'R$ ' + (val || 0).toLocaleString('pt-BR');
                            }
                        }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        theme: dark ? 'dark' : 'light',
                        y: {
                            formatter: function(val) {
                                return 'R$ ' + (val || 0).toLocaleString('pt-BR');
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
