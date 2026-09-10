<?php

namespace App\Livewire\RecentActivities;

use App\Enums\Permission;
use App\Enums\SaleStatus;
use App\Enums\TransactionType;
use App\Models\AccountBalance;
use App\Models\FinancialTransaction;
use App\Models\ProductPurchase;
use App\Models\Sale;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Index extends Component
{
    use AuthorizesRequests;
    use WireUiActions;
    use WithPagination;

    public string $type = 'all';

    public string $search = '';

    public string $period = 'all';

    public ?string $amount = null;

    public string $description = '';

    public string $adjustmentType = 'add';

    public bool $showAdjustmentModal = false;

    public bool $showConfirmCancelModal = false;

    public ?int $transactionToCancel = null;

    public function mount(): void
    {
        $this->authorize(Permission::ViewFinancialTransaction->value);
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPeriod(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['type', 'search', 'period']);
        $this->resetPage();
    }

    #[Computed]
    public function hasActiveFilters(): bool
    {
        return $this->type !== 'all' || filled($this->search) || $this->period !== 'all';
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function typeCounts(): array
    {
        $baseQuery = FinancialTransaction::query()
            ->when(filled($this->search), function ($query) {
                $query->where('description', 'like', '%' . trim($this->search) . '%');
            })
            ->when($this->period !== 'all', function ($query) {
                match ($this->period) {
                    'today'      => $query->whereDate('transaction_date', today()),
                    '7_days'     => $query->where('transaction_date', '>=', now()->subDays(7)),
                    '30_days'    => $query->where('transaction_date', '>=', now()->subDays(30)),
                    'this_month' => $query->whereMonth('transaction_date', now()->month)->whereYear('transaction_date', now()->year),
                    default      => null,
                };
            });

        $grouped = (clone $baseQuery)
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->all();

        return [
            'all'               => (int) (clone $baseQuery)->count(),
            'sale'              => (int) ($grouped[TransactionType::Sale->value] ?? 0),
            'purchase'          => (int) ($grouped[TransactionType::Purchase->value] ?? 0),
            'cancelled_sale'    => (int) ($grouped[TransactionType::CancelledSale->value] ?? 0),
            'manual_adjustment' => (int) ($grouped[TransactionType::ManualAdjustment->value] ?? 0),
        ];
    }

    #[Computed]
    public function transactions(): LengthAwarePaginator
    {
        return FinancialTransaction::query()
            ->when($this->type !== 'all', function ($query) {
                $typeMap = [
                    'sale'              => TransactionType::Sale,
                    'purchase'          => TransactionType::Purchase,
                    'cancelled_sale'    => TransactionType::CancelledSale,
                    'manual_adjustment' => TransactionType::ManualAdjustment,
                ];

                if (isset($typeMap[$this->type])) {
                    return $query->where('type', $typeMap[$this->type]);
                }
            })
            ->when(filled($this->search), function ($query) {
                $query->where('description', 'like', '%' . trim($this->search) . '%');
            })
            ->when($this->period !== 'all', function ($query) {
                match ($this->period) {
                    'today'      => $query->whereDate('transaction_date', today()),
                    '7_days'     => $query->where('transaction_date', '>=', now()->subDays(7)),
                    '30_days'    => $query->where('transaction_date', '>=', now()->subDays(30)),
                    'this_month' => $query->whereMonth('transaction_date', now()->month)->whereYear('transaction_date', now()->year),
                    default      => null,
                };
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
        $this->showAdjustmentModal = true;
    }

    public function fillAllBalance(): void
    {
        $currentBalanceInCents = $this->summary['current_balance'];

        if ($currentBalanceInCents > 0) {
            $this->amount = number_format($currentBalanceInCents / 100, 2, '.', '');
        }
    }

    public function saveAdjustment(): void
    {
        $this->authorize(Permission::CreateFinancialTransaction->value);

        $this->validate([
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $amountInCents = (int) round((float) $this->amount * 100);
        $isAdd = $this->adjustmentType === 'add';
        $finalAmount = $isAdd ? $amountInCents : -$amountInCents;

        FinancialTransaction::query()->create([
            'type'             => TransactionType::ManualAdjustment,
            'amount'           => $finalAmount,
            'description'      => $this->description,
            'transaction_date' => now(),
        ]);

        $balance = AccountBalance::singleton();

        if ($isAdd) {
            $balance->increment('current_balance', $amountInCents);
        }

        if (! $isAdd) {
            $balance->decrement('current_balance', $amountInCents);
        }

        $this->showAdjustmentModal = false;
        $this->notification()->success(
            title: __('finance.adjustment_success_title'),
            description: __('finance.adjustment_success_description'),
        );
    }

    public function confirmCancelAdjustment(int $id): void
    {
        $this->transactionToCancel = $id;
        $this->showConfirmCancelModal = true;
    }

    public function cancelAdjustment(): void
    {
        $this->authorize(Permission::DeleteFinancialTransaction->value);

        if (! $this->transactionToCancel) {
            return;
        }

        $transaction = FinancialTransaction::findOrFail($this->transactionToCancel);

        if ($transaction->type !== TransactionType::ManualAdjustment) {
            return;
        }

        DB::transaction(function () use ($transaction) {
            $balance = AccountBalance::singleton();
            $amountToRevert = abs($transaction->amount);

            if ($transaction->amount > 0) {
                $balance->decrement('current_balance', $amountToRevert);
            }

            if ($transaction->amount <= 0) {
                $balance->increment('current_balance', $amountToRevert);
            }

            $transaction->delete();
        });

        $this->showConfirmCancelModal = false;
        $this->transactionToCancel = null;

        $this->notification()->success(
            title: __('finance.cancel_success_title'),
            description: __('finance.cancel_success_description'),
        );
    }

    #[Computed]
    public function cancellingTransaction(): ?FinancialTransaction
    {
        if (! $this->transactionToCancel) {
            return null;
        }

        return FinancialTransaction::find($this->transactionToCancel);
    }

    public function render(): View
    {
        return view('livewire.recent-activities.index');
    }
}
