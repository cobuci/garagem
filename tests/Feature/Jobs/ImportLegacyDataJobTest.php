<?php

use App\Enums\Gender;
use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Jobs\ImportLegacyDataJob;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\assertDatabaseCount;

uses(RefreshDatabase::class);

it('imports customers from legacy sql', function () {
    Storage::fake();

    $sql = "
INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `gender`, `zipcode`, `street`, `number`, `district`, `created_at`, `updated_at`) VALUES
(1, 'Victor Cobuci', 'victor@example.com', '11999999999', 'M', '06755030', 'Rua Gilda de Abreu', '46', 'Jardim Santa Rosa', '2023-01-01 10:00:00', '2023-01-01 10:00:00'),
(2, 'Giovanna Cobuci', '', '', 'F', '06755030', 'Rua Gilda de Abreu', '46', 'Jardim Santa Rosa', NULL, NULL);
    ";

    $path = 'test-customers.sql';
    Storage::put($path, $sql);

    new ImportLegacyDataJob($path)->handle();

    expect(Customer::count())->toBe(2);

    $customer1 = Customer::find(1);
    expect($customer1->name)->toBe('Victor Cobuci')
        ->and($customer1->email)->toBe('victor@example.com')
        ->and($customer1->phone)->toBe('11999999999')
        ->and($customer1->gender)->toBe(Gender::Male)
        ->and($customer1->zip_code)->toBe('06755030')
        ->and($customer1->street)->toBe('Rua Gilda de Abreu')
        ->and($customer1->address)->toBe('46')
        ->and($customer1->neighborhood)->toBe('Jardim Santa Rosa');

    $customer2 = Customer::find(2);
    expect($customer2->name)->toBe('Giovanna Cobuci')
        ->and($customer2->email)->toBeNull()
        ->and($customer2->gender)->toBe(Gender::Female);
});

it('handles duplicate customer names by appending a suffix', function () {
    Storage::fake();

    Customer::create([
        'name'   => 'Lauro',
        'gender' => Gender::Male,
    ]);

    $sql = "
INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `gender`, `zipcode`, `street`, `number`, `district`, `created_at`, `updated_at`) VALUES
(52, 'Lauro', NULL, NULL, 'M', NULL, NULL, NULL, NULL, NULL, NULL),
(100, 'Lauro', NULL, NULL, 'M', NULL, NULL, NULL, NULL, NULL, NULL);
    ";

    $path = 'test-duplicate-names.sql';
    Storage::put($path, $sql);

    new ImportLegacyDataJob($path)->handle();

    expect(Customer::where('name', 'Lauro')->exists())->toBeTrue()
        ->and(Customer::where('name', 'Lauro 1')->exists())->toBeTrue()
        ->and(Customer::where('name', 'Lauro 2')->exists())->toBeTrue()
        ->and(Customer::count())->toBe(3);

});

it('imports sales and orders from legacy sql', function () {
    Storage::fake();

    $product = Product::factory()->create(['id' => 79]);
    Customer::factory()->create(['id' => 35]);

    $sql = "
INSERT INTO `sales` (`id`, `order_id`, `cost`, `discount`, `price`, `customer_id`, `customer_name`, `payment_method`, `payment_status`, `created_at`, `updated_at`) VALUES
(3, '623bd168c54c4', 4.50, NULL, 8.00, 35, 'Alessandra', 'PIX', '1', '2022-03-23 23:03:20', NULL);

INSERT INTO `orders` (`id`, `order_id`, `product_id`, `product_name`, `product_brand`, `unit_cost`, `unit_price`, `weight`, `amount`) VALUES
(3, '623bd168c54c4', 79, 'Refrigerante de Cola', 'Pepsi', 6.38, 8.00, '2L', 1);
    ";

    $path = 'test-sales.sql';
    Storage::put($path, $sql);

    new ImportLegacyDataJob($path)->handle();

    expect(Sale::count())->toBe(1)
        ->and(SaleItem::count())->toBe(1);

    $sale = Sale::first();
    expect($sale->payment_method)->toBe(PaymentMethod::Pix)
        ->and($sale->status)->toBe(SaleStatus::Paid)
        ->and((float) $sale->total_amount)->toBe(8.00)
        ->and((float) $sale->discount_amount)->toBe(0.00)
        ->and((float) $sale->net_amount)->toBe(8.00);

    $item = SaleItem::first();
    expect($item->sale_id)->toBe($sale->id)
        ->and($item->product_id)->toBe(79)
        ->and($item->quantity)->toBe(1)
        ->and((float) $item->unit_price)->toBe(8.00)
        ->and((float) $item->unit_cost)->toBe(6.38);
});

it('sets customer_id to null if customer does not exist', function () {
    Storage::fake();

    $sql = "
INSERT INTO `sales` (`id`, `order_id`, `cost`, `discount`, `price`, `customer_id`, `customer_name`, `payment_method`, `payment_status`, `created_at`, `updated_at`) VALUES
(3, '623bd168c54c4', 4.50, NULL, 8.00, 999, 'Non Existent', 'PIX', '1', '2022-03-23 23:03:20', NULL);
    ";

    $path = 'test-non-existent-customer.sql';
    Storage::put($path, $sql);

    new ImportLegacyDataJob($path)->handle();

    $sale = Sale::find(3);
    expect($sale->customer_id)->toBeNull();
});

it('skips sale items if product does not exist', function () {
    Storage::fake();

    $sql = "
INSERT INTO `sales` (`id`, `order_id`, `cost`, `discount`, `price`, `customer_id`, `customer_name`, `payment_method`, `payment_status`, `created_at`, `updated_at`) VALUES
(3, '623bd168c54c4', 4.50, NULL, 8.00, NULL, 'Alessandra', 'PIX', '1', '2022-03-23 23:03:20', NULL);

INSERT INTO `orders` (`id`, `order_id`, `product_id`, `product_name`, `product_brand`, `unit_cost`, `unit_price`, `weight`, `amount`) VALUES
(3, '623bd168c54c4', 999, 'Non Existent Product', 'Brand', 6.38, 8.00, '2L', 1);
    ";

    $path = 'test-non-existent-product.sql';
    Storage::put($path, $sql);

    (new ImportLegacyDataJob($path))->handle();

    expect(Sale::count())->toBe(1)
        ->and(SaleItem::count())->toBe(0);
});

it('rolls back everything if an error occurs during import', function () {
    Storage::fake();

    $sql = "
INSERT INTO `categories` (`id`, `name`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'Category 1', 'fa-tag', '2023-01-01 10:00:00', '2023-01-01 10:00:00');

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `gender`, `zipcode`, `street`, `number`, `district`, `created_at`, `updated_at`) VALUES
(1, 'Victor Cobuci', 'victor@example.com', '11999999999', 'M', '06755030', 'Rua Gilda de Abreu', '46', 'Jardim Santa Rosa', '2023-01-01 10:00:00', '2023-01-01 10:00:00');
    ";

    $path = 'test-rollback.sql';
    Storage::put($path, $sql);

    $job = Mockery::mock(ImportLegacyDataJob::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $job->filePath = $path;
    $job->shouldReceive('importSales')->andThrow(new Exception('Forced failure'));

    try {
        $job->handle();
    } catch (Exception $e) {
        // Expected
    }

    expect(Category::count())->toBe(0)
        ->and(Customer::count())->toBe(0);
});

it('does not trigger financial side effects during import', function () {
    Storage::fake();

    $sql = "
INSERT INTO `sales` (`id`, `order_id`, `cost`, `discount`, `price`, `customer_id`, `customer_name`, `payment_method`, `payment_status`, `created_at`, `updated_at`) VALUES
(3, '623bd168c54c4', 4.50, NULL, 8.00, NULL, 'Alessandra', 'PIX', '1', '2022-03-23 23:03:20', NULL);
    ";

    $path = 'test-no-events.sql';
    Storage::put($path, $sql);

    new ImportLegacyDataJob($path)->handle();

    expect(Sale::count())->toBe(1);

    assertDatabaseCount('financial_transactions', 0);
});

it('deletes the file from storage on failure', function () {
    Storage::fake();
    $path = 'test-failure-cleanup.sql';
    Storage::put($path, 'invalid sql');

    $job = new ImportLegacyDataJob($path);

    $job->failed(new Exception('Test exception'));

    Storage::disk()->assertMissing($path);
});
