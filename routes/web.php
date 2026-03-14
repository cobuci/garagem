<?php

use App\Livewire\Login;
use Illuminate\Support\Facades\Route;

Route::get('/', Login::class)->name('login');
