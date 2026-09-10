<div wire:key="average-ticket-evolution" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('reports.average_ticket_evolution.title') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('reports.average_ticket_evolution.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-native-select
                wire:model.live="period"
                noscroll
                :options="[
                    ['name' => __('reports.average_ticket_evolution.periods.last_3_months'), 'id' => 'last_3_months'],
                    ['name' => __('reports.average_ticket_evolution.periods.last_6_months'), 'id' => 'last_6_months'],
                    ['name' => __('reports.average_ticket_evolution.periods.last_12_months'), 'id' => 'last_12_months'],
                    ['name' => __('reports.average_ticket_evolution.periods.this_year'), 'id' => 'this_year'],
                    ['name' => __('reports.average_ticket_evolution.periods.last_year'), 'id' => 'last_year'],
                ]"
                option-label="name"
                option-value="id"
                class="w-full sm:w-48"
            />
        </div>
    </div>

    <div
        wire:ignore
        id="chart-average-ticket-evolution"
        wire:loading.delay.class="opacity-60 pointer-events-none transition-opacity duration-200"
        x-data="{
            categories: @entangle('chartDataArray.categories'),
            series: @entangle('chartDataArray.series'),
            chart: null,
            observer: null,
            init() {
                this.$nextTick(() => {
                    this.initChart();
                });

                this.$watch('series', () => this.updateChart());
                this.$watch('categories', () => this.updateChart());

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
                        xaxis: { categories: this.categories },
                        series: this.series
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
                        type: 'line',
                        height: 300,
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
                    series: this.series,
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    markers: {
                        size: 5,
                        hover: { size: 7 }
                    },
                    colors: ['#6366f1'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            shadeIntensity: 0.3,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 100]
                        }
                    },
                    dataLabels: { enabled: false },
                    grid: {
                        borderColor: dark ? 'rgba(156, 163, 175, 0.15)' : 'rgba(156, 163, 175, 0.1)',
                        strokeDashArray: 4,
                        padding: { left: 10, right: 10, top: 0, bottom: 0 }
                    },
                    xaxis: {
                        categories: this.categories,
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
                                return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        theme: dark ? 'dark' : 'light',
                        y: {
                            formatter: function(val) {
                                return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
