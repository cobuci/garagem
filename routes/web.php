<?php

use App\Livewire\BillsPayable\Index as BillsPayableIndex;
use App\Livewire\Customers\Index as CustomersIndex;
use App\Livewire\Customers\Show as CustomersShow;
use App\Livewire\Dashboard\Index;
use App\Livewire\Login;
use App\Livewire\Orders\Index as OrdersIndex;
use App\Livewire\Products\Index as ProductsIndex;
use App\Livewire\Sales\Create as SalesCreate;
use App\Livewire\Sales\Index as SalesIndex;
use App\Livewire\Settings\Index as SettingsIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', Login::class)->middleware('guest')->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Index::class)->name('dashboard');
    Route::get('/customers', CustomersIndex::class)->name('customers.index');
    Route::get('/customers/{customer}', CustomersShow::class)->name('customers.show');
    Route::get('/products', ProductsIndex::class)->name('products.index');
    Route::get('/sales', SalesIndex::class)->name('sales.index');
    Route::get('/sales/create', SalesCreate::class)->name('sales.create');
    Route::get('/orders', OrdersIndex::class)->name('orders.index');
    Route::get('/bills-payable', BillsPayableIndex::class)->name('bills-payable.index');
    Route::get('/settings', SettingsIndex::class)->name('settings.index');
});
