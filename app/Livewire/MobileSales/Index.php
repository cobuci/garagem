<?php

namespace App\Livewire\MobileSales;

use App\Enums\MobileSaleStatus;
use App\Enums\Permission as PermissionEnum;
use App\Models\MobileSale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

/**
 * @property-read LengthAwarePaginator $mobileSales
 * @property-read int $totalSales
 * @property-read int $totalPending
 * @property-read ?MobileSale $selectedMobileSale
 */
class Index extends Component
{
    use AuthorizesRequests;
    use WireUiActions;
    use WithPagination;

    public ?int $selectedMobileSaleId = null;

    public bool $showDetailsModal = false;

    public function mount(): void
    {
        $this->authorize(PermissionEnum::ViewSale->value);
    }

    #[Computed]
    public function mobileSales(): LengthAwarePaginator
    {
        return MobileSale::query()
            ->with(['customer', 'items.product'])
            ->latest('device_created_at')
            ->paginate(15);
    }

    #[Computed]
    public function totalSales(): int
    {
        return MobileSale::query()->count();
    }

    #[Computed]
    public function totalPending(): int
    {
        return MobileSale::query()
            ->where('status', MobileSaleStatus::Pending)
            ->count();
    }

    #[Computed]
    public function selectedMobileSale(): ?MobileSale
    {
        if (! $this->selectedMobileSaleId) {
            return null;
        }

        return MobileSale::with(['customer', 'items.product'])->find($this->selectedMobileSaleId);
    }

    public function showDetails(int $mobileSaleId): void
    {
        $this->selectedMobileSaleId = $mobileSaleId;
        $this->showDetailsModal = true;
    }

    public function delete(MobileSale $mobileSale): void
    {
        $this->authorize(PermissionEnum::DeleteSale->value);

        if ($mobileSale->status === MobileSaleStatus::Synced) {
            $this->notification()->error(
                title: __('mobile_sales.delete_error_title'),
                description: __('mobile_sales.delete_error_synced'),
            );

            return;
        }

        $mobileSale->delete();

        $this->showDetailsModal = false;
        $this->selectedMobileSaleId = null;
        unset($this->mobileSales, $this->totalSales, $this->totalPending);

        $this->notification()->success(
            title: __('mobile_sales.delete_success_title'),
            description: __('mobile_sales.delete_success_description'),
        );
    }

    public function render(): View
    {
        return view('livewire.mobile-sales.index');
    }
}
