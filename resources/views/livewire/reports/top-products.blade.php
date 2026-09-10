<div wire:key="top-products" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('reports.top_products') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('reports.most_sold_products') }}</p>
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

    <div
        wire:ignore
        id="chart-top-products"
        wire:loading.delay.class="opacity-60 pointer-events-none transition-opacity duration-200"
        x-data="{
            labels: @entangle('chartDataArray.labels'),
            quantity: @entangle('chartDataArray.quantity'),
            revenue: @entangle('chartDataArray.revenue'),
            cost: @entangle('chartDataArray.cost'),
            profit: @entangle('chartDataArray.profit'),
            chart: null,
            observer: null,
            formatMoney(value) {
                return 'R$ ' + (value || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },
            tooltipValue(dataPointIndex, quantity) {
                return '{{ __('reports.quantity') }}: ' + quantity + ' {{ __('reports.units') }}<br>' +
                    '{{ __('reports.total') }}: ' + this.formatMoney(this.revenue[dataPointIndex]) + '<br>' +
                    '{{ __('reports.cost') }}: ' + this.formatMoney(this.cost[dataPointIndex]) + '<br>' +
                    '{{ __('reports.profit') }}: ' + this.formatMoney(this.profit[dataPointIndex]);
            },
            tooltipOptions() {
                return {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    marker: { show: false },
                    y: {
                        title: { formatter: () => '' },
                        formatter: (val, { dataPointIndex }) => this.tooltipValue(dataPointIndex, val)
                    }
                };
            },
            init() {
                this.$nextTick(() => {
                    this.initChart();
                });

                this.$watch('quantity', () => this.updateChart());
                this.$watch('revenue', () => this.updateChart());
                this.$watch('cost', () => this.updateChart());
                this.$watch('profit', () => this.updateChart());
                this.$watch('labels', () => this.updateChart());

                this.observer = new MutationObserver(() => {
                    if (this.chart) {
                        const dark = document.documentElement.classList.contains('dark');
                        this.chart.updateOptions({
                            tooltip: this.tooltipOptions(),
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
                        series: [{
                            name: '{{ __('reports.quantity') }}',
                            data: this.quantity
                        }],
                        tooltip: this.tooltipOptions()
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
                            borderRadius: 6,
                            horizontal: true,
                            barHeight: '60%',
                            distributed: true,
                            dataLabels: {
                                position: 'top'
                            }
                        }
                    },
                    colors: ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899', '#f43f5e', '#ef4444', '#f97316', '#f59e0b', '#eab308'],
                    series: [{
                        name: '{{ __('reports.quantity') }}',
                        data: this.quantity
                    }],
                    dataLabels: {
                        enabled: true,
                        textAnchor: 'start',
                        style: {
                            colors: ['#fff'],
                            fontSize: '12px',
                            fontWeight: 600,
                            fontFamily: 'Instrument Sans, sans-serif'
                        },
                        formatter: function(val) {
                            return val;
                        },
                        offsetX: 0,
                    },
                    grid: {
                        borderColor: dark ? 'rgba(156, 163, 175, 0.15)' : 'rgba(156, 163, 175, 0.1)',
                        strokeDashArray: 4,
                        xaxis: { lines: { show: true } },
                        yaxis: { lines: { show: false } }
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
                            }
                        }
                    },
                    tooltip: this.tooltipOptions(),
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
