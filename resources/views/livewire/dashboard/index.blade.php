@use(App\Enums\Permission)
<div>
    <x-dashboard.header :name="$this->user->name ?? $this->user->email"/>

    @can(Permission::ViewAccountBalance->value)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <x-dashboard.stats-card
                :title="__('dashboard.total_balance')"
                :value="$this->totalBalance"
                icon="banknotes"
                color="blue"
            />

            <x-dashboard.stats-card
                :title="__('dashboard.sales_today')"
                :value="$this->dailyMetrics['sales']"
                :trend="$this->dailyMetrics['percent']"
                :profit="$this->dailyMetrics['profit']"
                :previous-profit="$this->dailyMetrics['previous_profit']"
                :pending-sales="$this->dailyMetrics['pending_sales']"
                icon="shopping-cart"
                color="emerald"
            />

            <x-dashboard.stats-card
                :title="__('dashboard.sales_month')"
                :value="$this->monthlyMetrics['sales']"
                :trend="$this->monthlyMetrics['percent']"
                :profit="$this->monthlyMetrics['profit']"
                :previous-profit="$this->monthlyMetrics['previous_profit']"
                :pending-sales="$this->monthlyMetrics['pending_sales']"
                icon="calendar-days"
                color="purple"
            />
        </div>
    @endcan

    @can(Permission::EditAccountBalance->value)
        <div class="mb-8">
            <x-dashboard.goal-card
                :target-balance="$targetBalance"
                :goal-metrics="$this->goalMetrics"
            />
        </div>
    @endcan

    @can(Permission::ViewFinancialTransaction->value)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2">
                <x-dashboard.chart-card :chart-data="$this->chartData"/>
            </div>

            <div class="space-y-6">
                <x-dashboard.performance-summary :chart-data="$this->chartData"/>
            </div>
        </div>
    @endcan

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @can(Permission::ViewFinancialTransaction->value)
            <div class="lg:col-span-2">
                <x-dashboard.recent-activities :activities="$this->recentActivities"/>
            </div>
        @endcan

        @can(Permission::ViewFinancialTransaction->value)
            <div>
                <x-dashboard.performance-indicators
                    :daily-metrics="$this->dailyMetrics"
                    :monthly-metrics="$this->monthlyMetrics"
                    :chart-data="$this->chartData"
                />
            </div>
        @endcan
    </div>
</div>
