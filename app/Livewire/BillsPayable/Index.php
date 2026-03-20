<?php

namespace App\Livewire\BillsPayable;

use App\Models\AccountBalance;
use App\Models\ProductPurchase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Index extends Component
{
    use WireUiActions;

    public string $search = '';

    public string $status = 'pending';

    public ?ProductPurchase $selectedBill = null;

    #[Computed]
    public function bills(): Collection
    {
        return ProductPurchase::with('product')
            ->when($this->status !== 'all', function ($query) {
                return $query->where('is_paid', $this->status === 'paid');
            })
            ->when($this->search, function ($query) {
                return $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('due_date', 'asc')
            ->get();
    }

    #[Computed]
    public function summary(): array
    {
        $now = now();
        $nextMonth = now()->addMonth();

        $totalDue = (int) ProductPurchase::where('is_paid', false)->sum('total_cost');

        $overdue = (int) ProductPurchase::where('is_paid', false)
            ->where('due_date', '<', $now->toDateString())
            ->sum('total_cost');

        $nextMonthTotal = (int) ProductPurchase::where('is_paid', false)
            ->whereYear('due_date', $nextMonth->year)
            ->whereMonth('due_date', $nextMonth->month)
            ->sum('total_cost');

        return [
            'total_due'  => $totalDue / 100,
            'overdue'    => $overdue / 100,
            'next_month' => $nextMonthTotal / 100,
        ];
    }

    public function confirmPayment(int $id): void
    {
        $this->selectedBill = ProductPurchase::with('product')->findOrFail($id);
        $this->js('$openModal(\'confirmPaymentModal\')');
    }

    public function confirmCancellation(int $id): void
    {
        $this->selectedBill = ProductPurchase::with('product')->findOrFail($id);
        $this->js('$openModal(\'confirmCancellationModal\')');
    }

    public function markAsPaid(): void
    {
        if (! $this->selectedBill || $this->selectedBill->is_paid) {
            return;
        }

        DB::transaction(function () {
            $this->selectedBill->update([
                'is_paid'      => true,
                'payment_date' => now(),
            ]);

            $balance = AccountBalance::singleton();
            $balance->decrement('current_balance', $this->selectedBill->getRawOriginal('total_cost'));
        });

        $this->js('$closeModal(\'confirmPaymentModal\')');
        $this->selectedBill = null;

        $this->notification()->success(
            title: __('bills_payable.actions.payment_success_title'),
            description: __('bills_payable.actions.payment_success_description'),
        );
    }

    public function cancelBill(): void
    {
        if (! $this->selectedBill) {
            return;
        }

        DB::transaction(function () {
            if ($this->selectedBill->is_paid) {
                $balance = AccountBalance::singleton();
                $balance->increment('current_balance', $this->selectedBill->getRawOriginal('total_cost'));
            }

            $product = $this->selectedBill->product;
            $product->decrement('stock_quantity', $this->selectedBill->quantity);

            $this->selectedBill->delete();
        });

        $this->js('$closeModal(\'confirmCancellationModal\')');
        $this->selectedBill = null;

        $this->notification()->success(
            title: __('bills_payable.actions.cancel_success_title'),
            description: __('bills_payable.actions.cancel_success_description'),
        );
    }

    public function render(): View
    {
        return view('livewire.bills-payable.index');
    }
}
