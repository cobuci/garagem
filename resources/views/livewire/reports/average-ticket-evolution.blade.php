<div wire:key="average-ticket-evolution" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('reports.average_ticket_evolution.title') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('reports.average_ticket_evolution.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-native-select
                wire:model.live="period"
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
        x-data="{
            categories: @entangle('chartDataArray.categories'),
            series: @entangle('chartDataArray.series'),
            chart: null,
            init() {
                this.$nextTick(() => {
                    this.initChart();
                });

                this.$watch('series', () => this.updateChart());
                this.$watch('categories', () => this.updateChart());

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

                this.chart = new ApexCharts(this.$refs.chart, {
                    chart: {
                        type: 'line',
                        height: 300,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        fontFamily: 'Inter, ui-sans-serif, system-ui',
                        background: 'transparent',
                        animations: { enabled: true }
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
                        borderColor: 'rgba(156, 163, 175, 0.1)',
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
                                return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
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
