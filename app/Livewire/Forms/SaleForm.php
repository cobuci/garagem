<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Livewire\Form;

class SaleForm extends Form
{
    public ?int $customerId = null;

    public string $paymentMethod = 'money';

    public bool $isGift = false;

    public bool $passFeeToCustomer = false;

    public float|string $discountAmount = 0;

    public string $status = 'pending';

    public array $items = [];

    public function addItem(int $productId): void
    {
        $product = Product::query()->find($productId);
        if (! $product) {
            return;
        }

        if (isset($this->items[$productId])) {
            $this->items[$productId]['quantity']++;
        } else {
            $this->items[$productId] = [
                'id'         => $product->id,
                'name'       => $product->name,
                'unit_price' => $product->getRawOriginal('sale_price'),
                'unit_cost'  => $product->getRawOriginal('unit_cost'),
                'quantity'   => 1,
            ];
        }
    }

    public function removeItem(int $productId): void
    {
        unset($this->items[$productId]);
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($productId);

            return;
        }

        if (isset($this->items[$productId])) {
            $this->items[$productId]['quantity'] = $quantity;
        }
    }

    public function subtotal(): int
    {
        return collect($this->items)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);
    }

    public function feePercentage(): float
    {
        $settings = Setting::singleton();

        if ($this->paymentMethod === 'credit_card') {
            return $settings->credit_card_fee ?? 0;
        }

        if ($this->paymentMethod === 'debit_card') {
            return $settings->debit_card_fee ?? 0;
        }

        return 0;
    }

    public function discountInCents(): int
    {
        return (int) round(((float) $this->discountAmount) * 100);
    }

    public function feeAmount(): int
    {
        $percentage = $this->feePercentage();
        if ($percentage <= 0) {
            return 0;
        }

        $baseAmount = $this->subtotal() - $this->discountInCents();

        return (int) round($baseAmount * ($percentage / 100));
    }

    public function totalAmount(): int
    {
        if ($this->isGift) {
            return 0;
        }

        $total = $this->subtotal() - $this->discountInCents();
        if ($this->passFeeToCustomer) {
            $total += $this->feeAmount();
        }

        return (int) $total;
    }

    public function netAmount(): int
    {
        if ($this->isGift) {
            return 0;
        }

        $total = $this->subtotal() - $this->discountInCents();
        if (! $this->passFeeToCustomer) {
            $total -= $this->feeAmount();
        }

        return (int) $total;
    }

    public function store(): void
    {
        if (empty($this->items)) {
            return;
        }

        DB::transaction(function (): void {
            $sale = Sale::query()->create([
                'customer_id'          => $this->customerId,
                'total_amount'         => $this->totalAmount() / 100,
                'discount_amount'      => $this->discountInCents() / 100,
                'fee_amount'           => $this->feeAmount() / 100,
                'fee_percentage'       => $this->feePercentage(),
                'pass_fee_to_customer' => $this->passFeeToCustomer,
                'net_amount'           => $this->netAmount() / 100,
                'payment_method'       => $this->paymentMethod,
                'status'               => $this->status,
                'is_gift'              => $this->isGift,
            ]);

            foreach ($this->items as $item) {
                $sale->items()->create([
                    'product_id' => $item['id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'] / 100,
                    'unit_cost'  => $item['unit_cost'] / 100,
                    'subtotal'   => ($item['unit_price'] * $item['quantity']) / 100,
                ]);

                $product = Product::query()->find($item['id']);
                if ($product instanceof Product && $product->stock_quantity !== null) {
                    $product->decrement('stock_quantity', $item['quantity']);
                }
            }
        });

        $this->reset(['items', 'customerId', 'discountAmount', 'isGift', 'passFeeToCustomer']);
    }
}
