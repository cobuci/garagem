<?php

namespace Tests\Feature\Settings;

use App\Livewire\Settings\Index;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render settings page', function () {
    $this->get(route('settings.index'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

it('can update general settings', function () {
    Livewire::test(Index::class)
        ->set('form.store_name', 'Minha Loja')
        ->call('save')
        ->assertHasNoErrors();

    $settings = Setting::singleton();
    expect($settings->store_name)->toBe('Minha Loja');
});

it('can update card fees', function () {
    Livewire::test(Index::class)
        ->set('form.credit_card_fee', 3.5)
        ->set('form.debit_card_fee', 1.5)
        ->call('save')
        ->assertHasNoErrors();

    $settings = Setting::singleton();
    expect((float) $settings->credit_card_fee)->toBe(3.5)
        ->and((float) $settings->debit_card_fee)->toBe(1.5);
});

it('can update address settings', function () {
    Livewire::test(Index::class)
        ->set('form.address', 'Rua Teste, 123')
        ->set('form.city', 'São Paulo')
        ->set('form.state', 'SP')
        ->set('form.zip_code', '01234-567')
        ->call('save')
        ->assertHasNoErrors();

    $settings = Setting::singleton();
    expect($settings->address)->toBe('Rua Teste, 123')
        ->and($settings->city)->toBe('São Paulo')
        ->and($settings->state)->toBe('SP')
        ->and($settings->zip_code)->toBe('01234-567');
});

it('validates card fees must be numbers', function () {
    Livewire::test(Index::class)
        ->set('form.credit_card_fee', 'abc')
        ->call('save')
        ->assertHasErrors(['form.credit_card_fee' => 'numeric']);
});

it('can update language preference', function () {
    app()->setLocale('pt_BR');
    $this->user->update(['locale' => 'pt_BR']);

    Livewire::test(Index::class)
        ->set('form.locale', 'en')
        ->assertRedirect(route('settings.index'));

    $this->user->refresh();
    expect($this->user->locale)->toBe('en');
    expect(session()->get('locale'))->toBe('en');
});

it('applies user locale from session or user model', function () {
    $this->user->update(['locale' => 'en']);

    $this->get(route('dashboard'))
        ->assertOk();

    expect(app()->getLocale())->toBe('en');

    session()->put('locale', 'pt_BR');

    $this->get(route('dashboard'))
        ->assertOk();

    expect(app()->getLocale())->toBe('pt_BR');
});
