<div>
    <x-dashboard.header :name="auth()->user()->name ?? auth()->user()->email" />

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        {{-- Total Balance Card --}}
        <x-dashboard.stats-card
            :title="__('dashboard.total_balance')"
            :value="$this->totalBalance"
            icon="banknotes"
            color="blue"
        />

        {{-- Monthly Goal Card --}}
        <x-dashboard.goal-card
            :target-balance="$targetBalance"
            :goal-metrics="$this->goalMetrics"
        />

        {{-- Daily Sales Card --}}
        <x-dashboard.stats-card
            :title="__('dashboard.sales_today')"
            :value="$this->dailyMetrics['sales']"
            :trend="$this->dailyMetrics['percent']"
            :profit="$this->dailyMetrics['profit']"
            :previous-profit="$this->dailyMetrics['previous_profit']"
            icon="shopping-cart"
            color="emerald"
        />

        {{-- Monthly Sales Card --}}
        <x-dashboard.stats-card
            :title="__('dashboard.sales_month')"
            :value="$this->monthlyMetrics['sales']"
            :trend="$this->monthlyMetrics['percent']"
            :profit="$this->monthlyMetrics['profit']"
            :previous-profit="$this->monthlyMetrics['previous_profit']"
            icon="calendar-days"
            color="purple"
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        {{-- Sales Overview Chart --}}
        <x-dashboard.chart-card :chart-data="$this->chartData" />

        {{-- Quick Stats / Info --}}
        <x-dashboard.performance-summary :chart-data="$this->chartData" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Recent Activities --}}
        <x-dashboard.recent-sales :sales="$this->recentSales" />

        {{-- Quick Stats / Mini Cards --}}
        <x-dashboard.performance-indicators
            :daily-metrics="$this->dailyMetrics"
            :monthly-metrics="$this->monthlyMetrics"
            :chart-data="$this->chartData"
        />
    </div>
</div>
