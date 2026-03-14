<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public Customer $customer;

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
    }

    #[Computed]
    public function orders(): Collection
    {
        return collect(range(1, 5))->map(fn ($i) => (object) [
            'id'           => $i,
            'total_amount' => 10000 * $i,
            'paid_amount'  => 5000 * $i,
            'status'       => $i % 2 === 0 ? 'paid' : 'pending',
            'created_at'   => now()->subDays($i),
        ]);
    }

    #[Computed]
    public function totalSpent(): int
    {
        return $this->orders->sum('total_amount');
    }

    #[Computed]
    public function totalDue(): int
    {
        return $this->orders->sum('total_amount') - $this->orders->sum('paid_amount');
    }

    public function render(): View
    {
        return view('livewire.customers.show');
    }
}
