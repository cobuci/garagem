<?php

use App\Models\Setting;
use App\Services\WeatherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('it returns null if city is not set', function () {
    $service = new WeatherService;
    expect($service->getCurrentWeather())->toBeNull();
});

test('it returns weather data if city is set', function () {
    Setting::singleton()->update(['city' => 'Sao Paulo', 'state' => 'SP']);

    Http::fake([
        'https://geocoding-api.open-meteo.com/*' => Http::response([
            'results' => [
                [
                    'latitude'  => -23.5505,
                    'longitude' => -46.6333,
                ],
            ],
        ]),
        'https://api.open-meteo.com/*' => Http::response([
            'current' => [
                'temperature_2m'       => 25.5,
                'relative_humidity_2m' => 60,
                'apparent_temperature' => 26.0,
                'weather_code'         => 0,
                'wind_speed_10m'       => 10.5,
            ],
            'daily' => [
                'time'               => [now()->toDateString()],
                'weather_code'       => [0],
                'temperature_2m_max' => [30],
                'temperature_2m_min' => [20],
                'precipitation_sum'  => [0],
            ],
        ]),
    ]);

    $service = new WeatherService;
    $weather = $service->getCurrentWeather();

    expect($weather)->not->toBeNull()
        ->and($weather['current']['temperature_2m'])->toBe(25.5)
        ->and($weather['current']['city'])->toBe('Sao Paulo');
});

test('it returns correct icon for weather code', function () {
    $service = new WeatherService;

    expect($service->getWeatherIcon(0))->toBe('sun')
        ->and($service->getWeatherIcon(1))->toBe('cloud')
        ->and($service->getWeatherIcon(51))->toBe('cloud')
        ->and($service->getWeatherIcon(95))->toBe('bolt')
        ->and($service->getWeatherIcon(4))->toBe('cloud');
});
