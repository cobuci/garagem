@use(App\Enums\Permission)
<div>
    <x-dashboard.header :name="$this->user->name ?? $this->user->email"/>

    @can(Permission::ViewAccountBalance->value)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <x-dashboard.stats-card
                :title="__('dashboard.total_balance')"
                :value="$this->totalBalance"
                icon="banknotes"
                color="sky"
            />

            <x-dashboard.stats-card
                :title="__('dashboard.sales_today')"
                :value="$this->dailyMetrics['sales']"
                :total="$this->dailyMetrics['total']"
                :trend="$this->dailyMetrics['percent']"
                :profit="$this->dailyMetrics['profit']"
                :total-profit="$this->dailyMetrics['total_profit']"
                :pending-sales="$this->dailyMetrics['pending_sales']"
                :pending-profit="$this->dailyMetrics['pending_profit']"
                icon="shopping-cart"
                color="emerald"
            />

            <x-dashboard.stats-card
                :title="__('dashboard.sales_month')"
                :value="$this->monthlyMetrics['sales']"
                :total="$this->monthlyMetrics['total']"
                :trend="$this->monthlyMetrics['percent']"
                :profit="$this->monthlyMetrics['profit']"
                :total-profit="$this->monthlyMetrics['total_profit']"
                :pending-sales="$this->monthlyMetrics['pending_sales']"
                :pending-profit="$this->monthlyMetrics['pending_profit']"
                icon="calendar-days"
                color="indigo"
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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 flex flex-col gap-8">
                <x-dashboard.chart-card :chart-data="$this->chartData"/>
                <x-dashboard.recent-activities :activities="$this->recentActivities"/>
            </div>

            <div class="flex flex-col gap-8">
                <x-dashboard.performance-summary :chart-data="$this->chartData" :monthly-metrics="$this->monthlyMetrics"/>
                <x-dashboard.performance-indicators
                    :daily-metrics="$this->dailyMetrics"
                    :monthly-metrics="$this->monthlyMetrics"
                    :chart-data="$this->chartData"
                />
            </div>
        </div>
    @endcan
</div>
