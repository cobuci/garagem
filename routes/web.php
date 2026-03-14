<?php

use App\Livewire\Dashboard\Index;
use App\Livewire\Login;
use Illuminate\Support\Facades\Route;

Route::get('/', Login::class)->name('login');
Route::get('/dashboard', Index::class)->name('dashboard')->middleware('auth');
