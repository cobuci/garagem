<?php

namespace App\Livewire\RecentActivities;

use App\Enums\SaleStatus;
use App\Enums\TransactionType;
use App\Models\AccountBalance;
use App\Models\FinancialTransaction;
use App\Models\ProductPurchase;
use App\Models\Sale;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Index extends Component
{
    use WireUiActions;
    use WithPagination;

    public string $type = 'all';

    public ?float $amount = null;

    public string $description = '';

    public string $adjustmentType = 'add';

    public function updatingType(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function transactions(): LengthAwarePaginator
    {
        return FinancialTransaction::query()
            ->when($this->type !== 'all', function ($query) {
                if ($this->type === 'sale') {
                    return $query->where('type', TransactionType::Sale);
                }
                if ($this->type === 'purchase') {
                    return $query->where('type', TransactionType::Purchase);
                }
                if ($this->type === 'cancelled_sale') {
                    return $query->where('type', TransactionType::CancelledSale);
                }
                if ($this->type === 'manual_adjustment') {
                    return $query->where('type', TransactionType::ManualAdjustment);
                }
            })
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);
    }

    #[Computed]
    public function summary(): array
    {
        $balance = AccountBalance::singleton();

        $totalDue = (int) ProductPurchase::where('is_paid', false)->sum('total_cost');

        $toReceive = (int) Sale::where('status', SaleStatus::Pending)->sum('net_amount');

        return [
            'current_balance' => (int) $balance->getRawOriginal('current_balance'),
            'total_due'       => $totalDue,
            'to_receive'      => $toReceive,
        ];
    }

    public function openAdjustmentModal(string $type): void
    {
        $this->adjustmentType = $type;
        $this->amount = null;
        $this->description = '';
        $this->js('$openModal(\'adjustmentModal\')');
    }

    public function saveAdjustment(): void
    {
        $this->validate([
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $amountInCents = (int) round($this->amount * 100);
        $finalAmount = $this->adjustmentType === 'add' ? $amountInCents : -$amountInCents;

        FinancialTransaction::query()->create([
            'type'             => TransactionType::ManualAdjustment,
            'amount'           => $finalAmount,
            'description'      => $this->description,
            'transaction_date' => now(),
        ]);

        $balance = AccountBalance::singleton();
        if ($this->adjustmentType === 'add') {
            $balance->increment('current_balance', $amountInCents);
        } else {
            $balance->decrement('current_balance', $amountInCents);
        }

        $this->js('$closeModal(\'adjustmentModal\')');
        $this->notification()->success(
            title: __('finance.adjustment_success_title'),
            description: __('finance.adjustment_success_description'),
        );
    }

    public function render(): View
    {
        return view('livewire.recent-activities.index');
    }
}
