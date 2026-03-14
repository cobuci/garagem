<?php

use App\Livewire\Customers\Index as CustomersIndex;
use App\Livewire\Dashboard\Index;
use App\Livewire\Login;
use Illuminate\Support\Facades\Route;

Route::get('/', Login::class)->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Index::class)->name('dashboard');
    Route::get('/customers', CustomersIndex::class)->name('customers.index');
});
