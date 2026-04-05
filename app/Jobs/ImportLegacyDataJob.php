<?php

namespace App\Jobs;

use App\Enums\Gender;
use App\Enums\SaleStatus;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportLegacyDataJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 0;

    public int $tries = 3;

    public function __construct(public string $filePath)
    {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        if (! Storage::exists($this->filePath)) {
            return;
        }

        ini_set('memory_limit', '512M');

        $sql = Storage::get($this->filePath);

        DB::transaction(function () use ($sql) {
            Sale::withoutEvents(function () use ($sql) {
                $this->importCategories($sql);
                $this->importProducts($sql);
                $this->importCustomers($sql);
                $this->importSales($sql);
                $this->importSaleItems($sql);
            });
        });

        Storage::delete($this->filePath);
    }

    public function failed(\Throwable $exception): void
    {
        Storage::delete($this->filePath);
    }

    protected function importCategories(string $sql): void
    {
        if (! preg_match('/INSERT INTO `categories` \(`id`, `name`, `icon`, `created_at`, `updated_at`\) VALUES\s*(.*?);/si', $sql, $matches)) {
            return;
        }

        $rows = $this->splitSqlRows($matches[1]);

        foreach ($rows as $index => $row) {
            $data = $this->parseSqlRow($row);

            if (count($data) < 5) {
                continue;
            }

            $id = (int) $data[0];
            $name = $data[1];
            $icon = isset($data[2]) ? $this->mapIcon($data[2]) : null;
            $createdAt = $this->parseDate($data[3]);
            $updatedAt = $this->parseDate($data[4]);

            Category::updateOrCreate(
                ['id' => $id],
                [
                    'name'       => $name,
                    'icon'       => $icon,
                    'sort_order' => $index + 1,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ],
            );
        }
    }

    protected function importCustomers(string $sql): void
    {
        if (! preg_match('/INSERT INTO `customers` \(`id`, `name`, `email`, `phone`, `gender`, `zipcode`, `street`, `number`, `district`, `created_at`, `updated_at`\) VALUES\s*(.*?);/si', $sql, $matches)) {
            return;
        }

        $rows = $this->splitSqlRows($matches[1]);

        $existingCustomerNames = Customer::pluck('name')->all();

        foreach ($rows as $row) {
            $data = $this->parseSqlRow($row);

            if (count($data) < 11) {
                continue;
            }

            $id = (int) $data[0];
            $name = $this->getUniqueCustomerName($data[1], $id, $existingCustomerNames);

            if (! in_array($name, $existingCustomerNames)) {
                $existingCustomerNames[] = $name;
            }

            $email = $this->cleanValue($data[2]);
            $phone = $this->cleanValue($data[3]);
            $genderRaw = strtoupper($data[4] ?? '');
            $zipCode = $this->cleanValue($data[5]);
            $street = $this->cleanValue($data[6]);
            $address = $this->cleanValue($data[7]);
            $neighborhood = $this->cleanValue($data[8]);
            $createdAt = $this->parseDate($data[9]);
            $updatedAt = $this->parseDate($data[10]);

            $gender = match (true) {
                str_starts_with($genderRaw, 'M') => Gender::Male,
                str_starts_with($genderRaw, 'F') => Gender::Female,
                default                          => null,
            };

            Customer::updateOrCreate(
                ['id' => $id],
                [
                    'name'         => $name,
                    'email'        => $email,
                    'phone'        => $phone,
                    'gender'       => $gender,
                    'zip_code'     => $zipCode,
                    'street'       => $street,
                    'address'      => $address,
                    'neighborhood' => $neighborhood,
                    'created_at'   => $createdAt,
                    'updated_at'   => $updatedAt,
                ],
            );
        }
    }

    protected function importSales(string $sql): void
    {
        if (! preg_match_all('/INSERT INTO `sales` \(`id`, `order_id`, `cost`, `discount`, `price`, `customer_id`, `customer_name`, `payment_method`, `payment_status`, `created_at`, `updated_at`\) VALUES\s*(.*?);/si', $sql, $matches)) {
            return;
        }

        $existingCustomerIds = Customer::pluck('id')->all();

        foreach ($matches[1] as $valuesBlock) {
            $rows = $this->splitSqlRows($valuesBlock);

            foreach ($rows as $row) {
                $data = $this->parseSqlRow($row);

                if (count($data) < 11) {
                    continue;
                }

                $id = (int) $data[0];
                $orderId = $data[1];
                $discount = $data[3] !== null && $data[3] !== 'NULL' ? (float) $data[3] : 0.00;
                $price = (float) $data[4];
                $customerId = $data[5] !== null && $data[5] !== 'NULL' ? (int) $data[5] : null;

                if ($customerId !== null && ! in_array($customerId, $existingCustomerIds)) {
                    $customerId = null;
                }

                $paymentMethod = $data[7];
                $paymentStatus = $data[8];
                $createdAt = $this->parseDate($data[9]);
                $updatedAt = $this->parseDate($data[10]);

                Sale::updateOrCreate(
                    ['id' => $id],
                    [
                        'customer_id'     => $customerId,
                        'payment_method'  => $paymentMethod,
                        'total_amount'    => $price + $discount,
                        'discount_amount' => $discount,
                        'net_amount'      => $price,
                        'status'          => $paymentStatus === '1' ? SaleStatus::Paid : SaleStatus::Pending,
                        'created_at'      => $createdAt,
                        'updated_at'      => $updatedAt,
                        'legacy_order_id' => $orderId,
                    ],
                );
            }
        }
    }

    protected function importSaleItems(string $sql): void
    {
        if (! preg_match_all('/INSERT INTO `orders` \(`id`, `order_id`, `product_id`, `product_name`, `product_brand`, `unit_cost`, `unit_price`, `weight`, `amount`\) VALUES\s*(.*?);/si', $sql, $matches)) {
            return;
        }

        $existingProductIds = Product::pluck('id')->all();
        $saleMap = Sale::whereNotNull('legacy_order_id')->pluck('id', 'legacy_order_id')->all();

        foreach ($matches[1] as $valuesBlock) {
            $rows = $this->splitSqlRows($valuesBlock);

            foreach ($rows as $row) {
                $data = $this->parseSqlRow($row);

                if (count($data) < 9) {
                    continue;
                }

                $legacyOrderId = $data[1];
                $productId = (int) $data[2];

                if (! in_array($productId, $existingProductIds)) {
                    continue;
                }

                $unitCost = (float) $data[5];
                $unitPrice = (float) $data[6];
                $amount = (int) $data[8];

                $saleId = $saleMap[$legacyOrderId] ?? null;

                if (! $saleId) {
                    continue;
                }

                SaleItem::updateOrCreate(
                    [
                        'sale_id'    => $saleId,
                        'product_id' => $productId,
                    ],
                    [
                        'quantity'   => $amount,
                        'unit_price' => $unitPrice,
                        'unit_cost'  => $unitCost,
                        'subtotal'   => $unitPrice * $amount,
                    ],
                );
            }
        }
    }

    protected function getUniqueCustomerName(string $name, int $id, array $existingNames = []): string
    {
        $existingCustomer = Customer::find($id);

        if ($existingCustomer) {
            return $existingCustomer->name;
        }

        $originalName = $name;
        $counter = 1;

        while (in_array($name, $existingNames)) {
            $name = "{$originalName} {$counter}";
            $counter++;
        }

        return $name;
    }

    protected function importProducts(string $sql): void
    {
        if (! preg_match_all('/INSERT INTO `products` \(`id`, `name`, `brand`, `weight`, `cost`, `sale`, `amount`, `expiration_date`, `category_id`, `upc`\) VALUES\s*(.*?);/si', $sql, $allMatches)) {
            return;
        }

        $existingCategoryIds = Category::pluck('id')->all();

        foreach ($allMatches[1] as $values) {
            $rows = $this->splitSqlRows($values);

            foreach ($rows as $row) {
                $data = $this->parseSqlRow($row);

                if (count($data) < 10) {
                    continue;
                }

                $category_id = (int) $data[8];

                if (! in_array($category_id, $existingCategoryIds)) {
                    Category::create([
                        'id'   => $category_id,
                        'name' => "Category {$category_id}",
                    ]);
                    $existingCategoryIds[] = $category_id;
                }

                Product::updateOrCreate(
                    ['id' => (int) $data[0]],
                    [
                        'category_id'     => $category_id,
                        'name'            => $data[1],
                        'brand'           => $data[2],
                        'weight'          => $data[3],
                        'upc'             => $this->cleanValue($data[9]),
                        'stock_quantity'  => (int) $data[6],
                        'unit_cost'       => (float) $data[4],
                        'sale_price'      => (float) $data[5],
                        'expiration_date' => $this->parseExpirationDate($data[7] ?? null),
                    ],
                );
            }
        }
    }

    protected function parseExpirationDate(?string $value): ?Carbon
    {
        if ($value === 'NULL' || empty($value) || str_starts_with($value, '0000-00-00') || str_starts_with($value, '-0001')) {
            return null;
        }

        return Carbon::parse($value);
    }

    protected function parseDate(?string $value): Carbon
    {
        if ($value === 'NULL' || $value === '0000-00-00 00:00:00' || empty($value)) {
            return Carbon::now();
        }

        return Carbon::parse($value);
    }

    protected function mapIcon(string $legacyIcon): string
    {
        if (! preg_match('/fa-([a-z-]+)/', $legacyIcon, $matches)) {
            return $legacyIcon;
        }

        if ($matches[1] === 'solid' && preg_match_all('/fa-([a-z-]+)/', $legacyIcon, $allMatches)) {
            return $allMatches[1][count($allMatches[1]) - 1];
        }

        return $matches[1];
    }

    protected function parseSqlRow(string $row): array
    {
        $columns = [];
        $current = '';
        $inString = false;
        $quoteChar = '';

        for ($i = 0; $i < strlen($row); $i++) {
            $char = $row[$i];

            if ($char === '\\') {
                if ($i + 1 < strlen($row)) {
                    $nextChar = $row[$i + 1];
                    if ($nextChar === "'" || $nextChar === '"' || $nextChar === '\\') {
                        $current .= $nextChar;
                        $i++;
                        continue;
                    }
                }
                $current .= $char;
            } elseif ($char === "'" || $char === '"') {
                if ($inString && $quoteChar === $char) {
                    if ($i + 1 < strlen($row) && $row[$i + 1] === $char) {
                        $current .= $char;
                        $i++;
                    } else {
                        $inString = false;
                    }
                } elseif (! $inString) {
                    $inString = true;
                    $quoteChar = $char;
                } else {
                    $current .= $char;
                }
            } elseif ($char === ',' && ! $inString) {
                $columns[] = $this->cleanValue($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }

        $columns[] = $this->cleanValue($current);

        return $columns;
    }

    protected function cleanValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if ($value === 'NULL' || $value === 'null' || $value === "''" || $value === '""' || $value === '') {
            return null;
        }

        return trim($value, "'\"");
    }

    protected function splitSqlRows(string $values): array
    {
        $rows = [];
        $current = '';
        $inString = false;
        $quoteChar = '';
        $depth = 0;

        for ($i = 0; $i < strlen($values); $i++) {
            $char = $values[$i];

            if ($char === '\\') {
                $current .= $char;
                if ($i + 1 < strlen($values)) {
                    $current .= $values[$i + 1];
                    $i++;
                }
            } elseif ($char === "'" || $char === '"') {
                $current .= $char;
                if ($inString && $quoteChar === $char) {
                    if ($i + 1 < strlen($values) && $values[$i + 1] === $char) {
                        $current .= $values[$i + 1];
                        $i++;
                    } else {
                        $inString = false;
                    }
                } elseif (! $inString) {
                    $inString = true;
                    $quoteChar = $char;
                }
            } elseif ($char === '(' && ! $inString) {
                $depth++;
                if ($depth === 1) {
                    $current = '';
                } else {
                    $current .= $char;
                }
            } elseif ($char === ')' && ! $inString) {
                $depth--;
                if ($depth === 0) {
                    $rows[] = $current;
                    $current = '';
                } else {
                    $current .= $char;
                }
            } else {
                $current .= $char;
            }
        }

        return array_filter(array_map('trim', $rows));
    }
}
