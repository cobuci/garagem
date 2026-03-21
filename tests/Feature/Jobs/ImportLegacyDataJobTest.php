<?php

use App\Enums\Gender;
use App\Jobs\ImportLegacyDataJob;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

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

    (new ImportLegacyDataJob($path))->handle();

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

    (new ImportLegacyDataJob($path))->handle();

    // Should have 3 Lauros: "Lauro", "Lauro 1", "Lauro 2"
    expect(Customer::where('name', 'Lauro')->exists())->toBeTrue()
        ->and(Customer::where('name', 'Lauro 1')->exists())->toBeTrue()
        ->and(Customer::where('name', 'Lauro 2')->exists())->toBeTrue();

    expect(Customer::count())->toBe(3);
});
