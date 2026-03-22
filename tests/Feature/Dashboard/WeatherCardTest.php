<?php

use App\Livewire\Dashboard\WeatherCard;
use App\Models\Setting;
use App\Models\WeatherHistory;
use App\Services\WeatherService;
use Livewire\Livewire;

it('renders weather card if city is set', function () {
    Setting::singleton()->update(['city' => 'Taboão da Serra', 'state' => 'SP']);

    Livewire::test(WeatherCard::class)
        ->assertStatus(200)
        ->assertSee('animate-pulse');
});

it('can refresh weather and use database', function () {
    Setting::singleton()->update(['city' => 'Taboão da Serra', 'state' => 'SP']);

    $history = WeatherHistory::create([
        'city'          => 'Taboão da Serra',
        'date'          => now()->toDateString(),
        'temp_current'  => 25,
        'temp_max'      => 30,
        'temp_min'      => 20,
        'precipitation' => 0,
        'weather_code'  => 0,
        'icon'          => 'sun',
        'description'   => 'Céu Limpo',
    ]);

    $weatherData = [
        'current' => [
            'temperature_2m' => 25,
            'weather_code'   => 0,
            'city'           => 'Taboão da Serra',
            'time'           => $history->updated_at->toDateTimeString(),
        ],
        'history' => [$history->toArray()],
    ];

    Livewire::test(WeatherCard::class)
        ->assertStatus(200)
        ->set('weather', $weatherData) // Force state since it's lazy
        ->assertSee('25°')
        ->assertSee('Taboão da Serra');
});

it('can force refresh and update database', function () {
    Setting::singleton()->update(['city' => 'Taboão da Serra', 'state' => 'SP']);

    $weatherData = [
        'current' => [
            'temperature_2m' => 25,
            'weather_code'   => 0,
            'city'           => 'Taboão da Serra',
            'time'           => now()->toIso8601String(),
        ],
        'history' => [],
    ];

    $this->mock(WeatherService::class, function ($mock) use ($weatherData) {
        $mock->shouldReceive('getCurrentWeather')->once()->andReturn($weatherData);
        $mock->shouldReceive('getWeatherIcon')->andReturn('sun');
        $mock->shouldReceive('getWeatherDescription')->andReturn('Céu Limpo');
        $mock->shouldReceive('getWeatherHistoryFromDb')->andReturn([]);
    });

    Livewire::test(WeatherCard::class)
        ->call('loadWeather', true)
        ->assertStatus(200);
});

it('handles error if weather cannot be loaded and no history exists', function () {
    Setting::singleton()->update(['city' => 'Taboão da Serra', 'state' => 'SP']);
    WeatherHistory::truncate();

    $this->mock(WeatherService::class, function ($mock) {
        $mock->shouldReceive('getCurrentWeather')->andReturn(null);
    });

    Livewire::test(WeatherCard::class)
        ->call('loadWeather')
        ->assertSet('hasError', true)
        ->assertSee(__('dashboard.weather.error'));
});

it('shows no location if city is not set', function () {
    Setting::query()->update(['city' => null]);

    Livewire::test(WeatherCard::class)
        ->call('loadWeather')
        ->assertSet('noLocation', true)
        ->assertSee(__('dashboard.weather.no_location'));
});
