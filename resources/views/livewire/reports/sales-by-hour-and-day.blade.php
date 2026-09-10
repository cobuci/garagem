<div wire:key="sales-by-hour-and-day" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 lg:col-span-2">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('reports.sales_by_hour_and_day') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('reports.sales_heatmap_overview') }}</p>
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
        id="chart-sales-by-hour-and-day"
        wire:loading.delay.class="opacity-60 pointer-events-none transition-opacity duration-200"
        x-data="{
            series: @entangle('chartDataArray.series'),
            chart: null,
            observer: null,
            init() {
                this.$nextTick(() => this.initChart());
                this.$watch('series', () => this.updateChart());
                this.observer = new MutationObserver(() => this.initChart());
                this.observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            },
            destroy() {
                if (this.observer) this.observer.disconnect();
                if (this.chart) this.chart.destroy();
            },
            isDarkMode() { return document.documentElement.classList.contains('dark'); },
            getColors() {
                const d = this.isDarkMode();
                return {
                    empty:  d ? '#374151' : '#f3f4f6',
                    low:    d ? '#065f46' : '#bbf7d0',
                    medium: d ? '#059669' : '#34d399',
                    high:   d ? '#10b981' : '#059669',
                    peak:   d ? '#6ee7b7' : '#065f46',
                    text:   d ? '#9ca3af' : '#6b7280',
                    bg:     d ? '#1f2937' : '#ffffff',
                };
            },
            updateChart() { if (this.chart) this.initChart(); },
            patchZeroCells() {
                const color = this.getColors().empty;
                const ref = this.$refs.chart;
                if (!ref) return;
                ref.querySelectorAll('.apexcharts-heatmap-rect').forEach(function(el) {
                    if (el.getAttribute('val') === '0') {
                        el.setAttribute('fill', color);
                        el.setAttribute('stroke', color);
                    }
                });
            },
            initChart() {
                if (!this.$refs.chart) { setTimeout(() => this.initChart(), 50); return; }
                if (this.chart) { this.chart.destroy(); this.chart = null; }
                const c = this.getColors();
                const noData = '{{ __('reports.heatmap.no_data') }}';
                const transactions = '{{ __('reports.pdf.transactions') }}';
                const self = this;
                this.chart = new ApexCharts(this.$refs.chart, {
                    chart: {
                        type: 'heatmap',
                        height: 550,
                        toolbar: { show: false },
                        fontFamily: 'Instrument Sans, sans-serif',
                        background: c.bg,
                        animations: { enabled: false }
                    },
                    noData: {
                        text: noData,
                        align: 'center',
                        verticalAlign: 'middle',
                        style: {
                            color: '#9ca3af',
                            fontSize: '14px',
                            fontFamily: 'Instrument Sans, sans-serif'
                        }
                    },
                    dataLabels: { enabled: false },
                    series: this.series,
                    xaxis: { type: 'category', axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: c.text, fontSize: '12px', fontWeight: 500, fontFamily: 'Instrument Sans, sans-serif' } } },
                    yaxis: { labels: { style: { colors: c.text, fontSize: '12px', fontFamily: 'Instrument Sans, sans-serif' } } },
                    plotOptions: {
                        heatmap: {
                            shadeIntensity: 0,
                            radius: 3,
                            useFillColorAsStroke: true,
                            colorScale: {
                                min: 0, max: 5000,
                                ranges: [
                                    { from: 0,       to: 0,         color: c.empty,  name: noData },
                                    { from: 0.01,    to: 100,       color: c.low,    name: '{{ __('reports.heatmap.low') }}' },
                                    { from: 100.01,  to: 500,       color: c.medium, name: '{{ __('reports.heatmap.medium') }}' },
                                    { from: 500.01,  to: 2000,      color: c.high,   name: '{{ __('reports.heatmap.high') }}' },
                                    { from: 2000.01, to: 100000000, color: c.peak,   name: '{{ __('reports.heatmap.peak') }}' },
                                ]
                            }
                        }
                    },
                    stroke: { show: true, width: 1, colors: [c.bg] },
                    grid: { show: false, padding: { right: 20 } },
                    legend: { show: true, position: 'top', horizontalAlign: 'left', fontSize: '12px', fontFamily: 'Instrument Sans, sans-serif', labels: { colors: c.text }, markers: { size: 10, shape: 'square', offsetX: -2 }, itemMargin: { horizontal: 12 } },
                    tooltip: {
                        theme: this.isDarkMode() ? 'dark' : 'light',
                        y: {
                            formatter: function(val, opts) {
                                if (val === 0) return noData;
                                const sales = opts.w.config.series[opts.seriesIndex].data[opts.dataPointIndex].sales || 0;
                                return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) + ' (' + sales + ' ' + transactions + ')';
                            }
                        }
                    }
                });
                this.chart.render().then(() => self.patchZeroCells());
            }
        }"
        class="w-full"
    >
        <div x-ref="chart"></div>
    </div>
</div>
