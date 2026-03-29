<?php

namespace App\Livewire\Layout;

use App\Enums\Permission as PermissionEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Sidebar extends Component
{
    public function switchUser(int $userId): void
    {
        if (app()->isProduction()) {
            return;
        }

        Auth::loginUsingId($userId);
        $this->redirect(request()->header('Referer', route('dashboard')));
    }

    #[Computed]
    public function availableUsers(): Collection
    {
        return User::query()->limit(5)->get();
    }

    #[Computed]
    public function menuGroups(): array
    {
        $user = Auth::user();

        $groups = [
            [
                'title' => __('sidebar.general'),
                'items' => [
                    [
                        'label'      => __('sidebar.dashboard'),
                        'icon'       => 'home',
                        'route'      => 'dashboard',
                        'active'     => request()->routeIs('dashboard'),
                        'permission' => null,
                    ],
                    [
                        'label'      => __('sidebar.recent_activities'),
                        'icon'       => 'list-bullet',
                        'route'      => 'recent-activities.index',
                        'active'     => request()->routeIs('recent-activities.*'),
                        'permission' => PermissionEnum::ViewFinancialTransaction->value,
                    ],
                ],
            ],
            [
                'title' => __('sidebar.customers_category'),
                'items' => [
                    [
                        'label'      => __('sidebar.customers'),
                        'icon'       => 'users',
                        'route'      => 'customers.index',
                        'active'     => request()->routeIs('customers.*'),
                        'permission' => PermissionEnum::ViewCustomer->value,
                    ],
                ],
            ],
            [
                'title' => __('sidebar.inventory'),
                'items' => [
                    [
                        'label'      => __('sidebar.products'),
                        'icon'       => 'tag',
                        'route'      => 'products.index',
                        'active'     => request()->routeIs('products.*'),
                        'permission' => PermissionEnum::ViewProduct->value,
                    ],
                ],
            ],
            [
                'title' => __('sidebar.finance'),
                'items' => [
                    [
                        'label'      => __('sidebar.pos'),
                        'icon'       => 'shopping-bag',
                        'route'      => 'sales.create',
                        'active'     => request()->routeIs('sales.create'),
                        'permission' => PermissionEnum::CreateSale->value,
                    ],
                    [
                        'label'      => __('sidebar.orders'),
                        'icon'       => 'shopping-cart',
                        'route'      => 'sales.index',
                        'active'     => request()->routeIs('sales.index'),
                        'permission' => PermissionEnum::ViewSale->value,
                    ],
                    [
                        'label'      => __('sidebar.mobile_sales'),
                        'icon'       => 'device-phone-mobile',
                        'route'      => 'mobile-sales.index',
                        'active'     => request()->routeIs('mobile-sales.*'),
                        'permission' => PermissionEnum::ViewSale->value,
                    ],
                    [
                        'label'      => __('sidebar.bills_payable'),
                        'icon'       => 'banknotes',
                        'route'      => 'bills-payable.index',
                        'active'     => request()->routeIs('bills-payable.*'),
                        'permission' => PermissionEnum::ViewFinancialTransaction->value,
                    ],
                ],
            ],
            [
                'title' => __('sidebar.reports_category'),
                'items' => [
                    [
                        'label'      => __('sidebar.reports'),
                        'icon'       => 'chart-bar',
                        'route'      => 'reports.index',
                        'active'     => request()->routeIs('reports.*'),
                        'permission' => PermissionEnum::ViewReport->value,
                    ],
                ],
            ],
        ];

        return collect($groups)->map(function ($group) use ($user) {
            $group['items'] = collect($group['items'])->filter(function ($item) use ($user) {
                if (empty($item['permission'])) {
                    return true;
                }

                return $user?->can($item['permission']);
            })->values()->all();

            return $group;
        })->filter(fn ($group) => ! empty($group['items']))->values()->all();
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect(route('login'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.layout.sidebar');
    }
}
