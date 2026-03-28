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

test('getWeatherIcon covers all code ranges', function () {
    $service = new WeatherService;

    expect($service->getWeatherIcon(2))->toBe('cloud')
        ->and($service->getWeatherIcon(3))->toBe('cloud')
        ->and($service->getWeatherIcon(45))->toBe('eye-slash')
        ->and($service->getWeatherIcon(48))->toBe('eye-slash')
        ->and($service->getWeatherIcon(53))->toBe('cloud')
        ->and($service->getWeatherIcon(55))->toBe('cloud')
        ->and($service->getWeatherIcon(61))->toBe('cloud')
        ->and($service->getWeatherIcon(63))->toBe('cloud')
        ->and($service->getWeatherIcon(65))->toBe('cloud')
        ->and($service->getWeatherIcon(71))->toBe('variable')
        ->and($service->getWeatherIcon(73))->toBe('variable')
        ->and($service->getWeatherIcon(75))->toBe('variable')
        ->and($service->getWeatherIcon(80))->toBe('cloud')
        ->and($service->getWeatherIcon(81))->toBe('cloud')
        ->and($service->getWeatherIcon(82))->toBe('cloud')
        ->and($service->getWeatherIcon(99))->toBe('bolt');
});

test('getWeatherDescription returns correct label for each code range', function () {
    $service = new WeatherService;

    expect($service->getWeatherDescription(0))->toBe(__('dashboard.weather.clear_sky'))
        ->and($service->getWeatherDescription(1))->toBe(__('dashboard.weather.mainly_clear'))
        ->and($service->getWeatherDescription(2))->toBe(__('dashboard.weather.partly_cloudy'))
        ->and($service->getWeatherDescription(3))->toBe(__('dashboard.weather.overcast'))
        ->and($service->getWeatherDescription(45))->toBe(__('dashboard.weather.fog'))
        ->and($service->getWeatherDescription(48))->toBe(__('dashboard.weather.fog'))
        ->and($service->getWeatherDescription(51))->toBe(__('dashboard.weather.drizzle'))
        ->and($service->getWeatherDescription(53))->toBe(__('dashboard.weather.drizzle'))
        ->and($service->getWeatherDescription(55))->toBe(__('dashboard.weather.drizzle'))
        ->and($service->getWeatherDescription(61))->toBe(__('dashboard.weather.rain'))
        ->and($service->getWeatherDescription(63))->toBe(__('dashboard.weather.rain'))
        ->and($service->getWeatherDescription(65))->toBe(__('dashboard.weather.rain'))
        ->and($service->getWeatherDescription(71))->toBe(__('dashboard.weather.snow'))
        ->and($service->getWeatherDescription(73))->toBe(__('dashboard.weather.snow'))
        ->and($service->getWeatherDescription(75))->toBe(__('dashboard.weather.snow'))
        ->and($service->getWeatherDescription(80))->toBe(__('dashboard.weather.showers'))
        ->and($service->getWeatherDescription(81))->toBe(__('dashboard.weather.showers'))
        ->and($service->getWeatherDescription(82))->toBe(__('dashboard.weather.showers'))
        ->and($service->getWeatherDescription(95))->toBe(__('dashboard.weather.thunderstorm'))
        ->and($service->getWeatherDescription(99))->toBe(__('dashboard.weather.thunderstorm'))
        ->and($service->getWeatherDescription(10))->toBe(__('dashboard.weather.unknown'));
});
