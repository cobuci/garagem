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
                    [
                        'label'  => __('sidebar.bills_payable'),
                        'icon'   => 'banknotes',
                        'route'  => 'bills-payable.index',
                        'active' => request()->routeIs('bills-payable.*'),
                    ],
                ],
            ],
            [
                'title' => __('sidebar.sales'),
                'items' => [
                    [
                        'label'  => __('sidebar.pos'),
                        'icon'   => 'shopping-bag',
                        'route'  => 'sales.create',
                        'active' => request()->routeIs('sales.create'),
                    ],
                    [
                        'label'  => __('sidebar.orders'),
                        'icon'   => 'list-bullet',
                        'route'  => 'sales.index',
                        'active' => request()->routeIs('sales.index'),
                    ],
                    [
                        'label'  => __('sidebar.customers'),
                        'icon'   => 'users',
                        'route'  => 'customers.index',
                        'active' => request()->routeIs('customers.*'),
                    ],
                    [
                        'label'  => __('sidebar.reports'),
                        'icon'   => 'chart-bar',
                        'route'  => '#',
                        'active' => false,
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
