<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('reports.sales_by_payment_method') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('reports.payment_methods_overview') }}</p>
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

    <div
        x-data="{
            labels: @js($this->chartData['labels']),
            series: @js($this->chartData['series']),
            chart: null,
            init() {
                this.$nextTick(() => {
                    this.initChart();
                });

                this.$watch('series', (value) => {
                    if (this.chart) {
                        this.chart.updateSeries(value);
                    }
                });

                this.$watch('labels', (value) => {
                    if (this.chart) {
                        this.chart.updateOptions({
                            labels: value
                        });
                    }
                });
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
                        type: 'donut',
                        height: 350,
                        fontFamily: 'Inter, ui-sans-serif, system-ui',
                        background: 'transparent',
                        animations: { enabled: true }
                    },
                    series: this.series,
                    labels: this.labels,
                    colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                    stroke: {
                        show: true,
                        width: 2,
                        colors: [document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff']
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '14px',
                                        fontWeight: 600,
                                        color: '#9ca3af'
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '20px',
                                        fontWeight: 700,
                                        color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827',
                                        formatter: function(val) {
                                            return 'R$ ' + parseFloat(val).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                                        }
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        color: '#9ca3af',
                                        formatter: function (w) {
                                            return 'R$ ' + w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                                        }
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: { enabled: false },
                    legend: {
                        position: 'bottom',
                        fontFamily: 'Inter, ui-sans-serif, system-ui',
                        fontSize: '12px',
                        fontWeight: 500,
                        labels: {
                            colors: '#9ca3af'
                        },
                        markers: {
                            width: 10,
                            height: 10,
                            radius: 12
                        },
                        itemMargin: {
                            horizontal: 10,
                            vertical: 5
                        }
                    },
                    responsive: [
                        {
                            breakpoint: 640,
                            options: {
                                chart: {
                                    height: 300
                                },
                                legend: {
                                    position: 'bottom',
                                    fontSize: '11px'
                                },
                                plotOptions: {
                                    pie: {
                                        donut: {
                                            size: '65%',
                                            labels: {
                                                name: { fontSize: '12px' },
                                                value: { fontSize: '16px' },
                                                total: { fontSize: '12px' }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    ],
                    tooltip: {
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                        y: {
                            formatter: function(val) {
                                return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                });
                this.chart.render();
            }
        }"
        class="w-full"
    >
        <div x-ref="chart"></div>
    </div>
</div>
