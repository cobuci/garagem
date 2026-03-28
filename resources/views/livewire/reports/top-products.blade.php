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
        x-data="{
            labels: @entangle('chartDataArray.labels'),
            quantity: @entangle('chartDataArray.quantity'),
            revenue: @entangle('chartDataArray.revenue'),
            chart: null,
            init() {
                this.$nextTick(() => {
                    this.initChart();
                });

                this.$watch('quantity', () => this.updateChart());
                this.$watch('revenue', () => this.updateChart());
                this.$watch('labels', () => this.updateChart());
            },
            updateChart() {
                if (this.chart) {
                    this.chart.updateOptions({
                        xaxis: { categories: this.labels },
                        series: [{
                            name: '{{ __('reports.quantity') }}',
                            data: this.quantity
                        }],
                        tooltip: {
                            y: {
                                formatter: (val, { seriesIndex, dataPointIndex, w }) => {
                                    let rev = this.revenue[dataPointIndex] || 0;
                                    return val + ' {{ __('reports.units') }} (R$ ' + rev.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ')';
                                }
                            }
                        }
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
                        type: 'bar',
                        height: 350,
                        toolbar: { show: false },
                        fontFamily: 'Inter, ui-sans-serif, system-ui',
                        background: 'transparent',
                        animations: { enabled: true }
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
                            fontSize: '11px',
                            fontWeight: 600
                        },
                        formatter: function(val) {
                            return val;
                        },
                        offsetX: 0,
                    },
                    grid: {
                        borderColor: 'rgba(156, 163, 175, 0.1)',
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
                                fontSize: '11px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#9ca3af',
                                fontSize: '11px'
                            }
                        }
                    },
                    tooltip: {
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                        y: {
                            formatter: (val, { seriesIndex, dataPointIndex, w }) => {
                                let rev = this.revenue[dataPointIndex];
                                return val + ' {{ __('reports.units') }} (R$ ' + rev.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) + ')';
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
