<?php

namespace App\Livewire\Layout;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Sidebar extends Component
{
    #[Computed]
    public function menuGroups(): array
    {
        return [
            [
                'title' => __('sidebar.general'),
                'items' => [
                    [
                        'label'  => __('sidebar.dashboard'),
                        'icon'   => 'home',
                        'route'  => 'dashboard',
                        'active' => request()->routeIs('dashboard'),
                    ],
                    [
                        'label'  => __('sidebar.recent_activities'),
                        'icon'   => 'list-bullet',
                        'route'  => 'recent-activities.index',
                        'active' => request()->routeIs('recent-activities.*'),
                    ],
                ],
            ],
            [
                'title' => __('sidebar.customers_category'),
                'items' => [
                    [
                        'label'  => __('sidebar.customers'),
                        'icon'   => 'users',
                        'route'  => 'customers.index',
                        'active' => request()->routeIs('customers.*'),
                    ],
                ],
            ],
            [
                'title' => __('sidebar.inventory'),
                'items' => [
                    [
                        'label'  => __('sidebar.products'),
                        'icon'   => 'tag',
                        'route'  => 'products.index',
                        'active' => request()->routeIs('products.*'),
                    ],
                ],
            ],
            [
                'title' => __('sidebar.finance'),
                'items' => [
                    [
                        'label'  => __('sidebar.pos'),
                        'icon'   => 'shopping-bag',
                        'route'  => 'sales.create',
                        'active' => request()->routeIs('sales.create'),
                    ],
                    [
                        'label'  => __('sidebar.orders'),
                        'icon'   => 'shopping-cart',
                        'route'  => 'sales.index',
                        'active' => request()->routeIs('sales.index'),
                    ],
                    [
                        'label'  => __('sidebar.bills_payable'),
                        'icon'   => 'banknotes',
                        'route'  => 'bills-payable.index',
                        'active' => request()->routeIs('bills-payable.*'),
                    ],
                ],
            ],
            [
                'title' => __('sidebar.reports_category'),
                'items' => [
                    [
                        'label'  => __('sidebar.reports'),
                        'icon'   => 'chart-bar',
                        'route'  => 'reports.index',
                        'active' => request()->routeIs('reports.*'),
                    ],
                ],
            ],
        ];
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
