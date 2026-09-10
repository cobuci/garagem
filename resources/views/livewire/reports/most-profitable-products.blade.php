<div wire:key="most-profitable-products" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('reports.most_profitable_products') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('reports.products_with_highest_profit') }}</p>
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
        id="chart-most-profitable-products"
        wire:loading.delay.class="opacity-60 pointer-events-none transition-opacity duration-200"
        x-data="{
            labels: @entangle('chartDataArray.labels'),
            profit: @entangle('chartDataArray.profit'),
            quantity: @entangle('chartDataArray.quantity'),
            avg_margin: @entangle('chartDataArray.avg_margin'),
            chart: null,
            observer: null,
            init() {
                this.$nextTick(() => {
                    this.initChart();
                });

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
                        series: [{
                            name: '{{ __('reports.profit') }}',
                            data: this.profit
                        }],
                        tooltip: {
                            theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                            y: {
                                formatter: (val, { seriesIndex, dataPointIndex, w }) => {
                                    let qty = this.quantity[dataPointIndex] || 0;
                                    let margin = this.avg_margin[dataPointIndex] || 0;
                                    return 'R$ ' + (val || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +
                                           ' (' + qty + ' {{ __('reports.units') }} | Avg: R$ ' +
                                           margin.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ')';
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
                    colors: ['#065f46', '#047857', '#059669', '#10b981', '#34d399', '#14b8a6', '#0d9488', '#0f766e', '#0e7490', '#0284c7'],
                    series: [{
                        name: '{{ __('reports.profit') }}',
                        data: this.profit
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
                            return 'R$ ' + (val || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
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
                            },
                            formatter: function(val) {
                                return 'R$ ' + val;
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
                    tooltip: {
                        theme: dark ? 'dark' : 'light',
                        y: {
                            formatter: (val, { seriesIndex, dataPointIndex, w }) => {
                                let qty = this.quantity[dataPointIndex] || 0;
                                let margin = this.avg_margin[dataPointIndex] || 0;
                                return 'R$ ' + (val || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) +
                                       ' (' + qty + ' {{ __('reports.units') }} | Avg: R$ ' +
                                       margin.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) + ')';
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
