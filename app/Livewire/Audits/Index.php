<?php

namespace App\Livewire\Audits;

use App\Enums\Permission;
use App\Models\AccountBalance;
use App\Models\Category;
use App\Models\Customer;
use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use OwenIt\Auditing\Models\Audit;

/**
 * @property-read LengthAwarePaginator $audits
 * @property-read Collection<int, User> $users
 * @property-read ?Audit $selectedAudit
 */
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(as: 'event')]
    public string $event = '';

    #[Url(as: 'model')]
    public string $auditable = '';

    #[Url(as: 'user')]
    public int|string $userId = '';

    public ?int $selectedAuditId = null;

    public bool $showDetailsModal = false;

    /** @var array<string, class-string> */
    public const AUDITABLE_MODELS = [
        'Product'              => Product::class,
        'Customer'             => Customer::class,
        'Category'             => Category::class,
        'Sale'                 => Sale::class,
        'SaleItem'             => SaleItem::class,
        'ProductPurchase'      => ProductPurchase::class,
        'FinancialTransaction' => FinancialTransaction::class,
        'Setting'              => Setting::class,
        'AccountBalance'       => AccountBalance::class,
    ];

    public function mount(): void
    {
        $this->authorize(Permission::ViewAdmin->value);
    }

    public function filterByEvent(string $event): void
    {
        $this->event = $event;
        $this->resetPage();
    }

    public function updatedAuditable(): void
    {
        $this->resetPage();
    }

    public function updatedUserId(): void
    {
        $this->resetPage();
    }

    public function showDetails(int $auditId): void
    {
        $this->selectedAuditId = $auditId;
        $this->showDetailsModal = true;
    }

    #[Computed]
    public function audits(): LengthAwarePaginator
    {
        return Audit::query()
            ->with('user')
            ->when($this->event, fn ($query) => $query->where('event', $this->event))
            ->when($this->auditable, fn ($query) => $query->where('auditable_type', self::AUDITABLE_MODELS[$this->auditable] ?? $this->auditable))
            ->when($this->userId, fn ($query) => $query->where('user_id', $this->userId))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function users(): Collection
    {
        return User::query()->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function selectedAudit(): ?Audit
    {
        if (! $this->selectedAuditId) {
            return null;
        }

        return Audit::with('user')->find($this->selectedAuditId);
    }

    public function render(): View
    {
        return view('livewire.audits.index');
    }
}
