<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\WeatherHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    private const BASE_URL = 'https://api.open-meteo.com/v1/forecast';

    private const GEOCODING_URL = 'https://geocoding-api.open-meteo.com/v1/search';

    public function getCurrentWeather(): ?array
    {
        $settings = Setting::singleton();

        if (empty($settings->city)) {
            return null;
        }

        try {
            $location = $this->getCoordinates($settings->city, $settings->state);

            if (! $location) {
                return null;
            }

            $data = $this->fetchWeatherData($location['latitude'], $location['longitude']);

            if (! $data) {
                return null;
            }

            $current = $data['current'];
            $current['city'] = $settings->city;

            $this->saveWeatherHistory($settings->city, $current, $data['daily']);

            return [
                'current' => $current,
                'history' => $this->getWeatherHistoryFromDb($settings->city),
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching weather', ['message' => $e->getMessage()]);

            return null;
        }
    }

    private function fetchWeatherData(float $latitude, float $longitude): ?array
    {
        $response = Http::get(self::BASE_URL, [
            'latitude'      => $latitude,
            'longitude'     => $longitude,
            'current'       => 'temperature_2m,relative_humidity_2m,apparent_temperature,is_day,precipitation,weather_code,wind_speed_10m',
            'daily'         => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum',
            'timezone'      => 'auto',
            'past_days'     => 7,
            'forecast_days' => 1,
        ]);

        if ($response->failed()) {
            Log::error('Weather API failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return null;
        }

        return $response->json();
    }

    private function saveWeatherHistory(string $city, array $current, array $daily): void
    {
        foreach ($daily['time'] as $index => $time) {
            $isToday = Carbon::parse($time)->isToday();
            $code = $daily['weather_code'][$index];

            WeatherHistory::updateOrCreate(
                ['city' => $city, 'date' => $time],
                [
                    'temp_current'  => $isToday ? $current['temperature_2m'] : null,
                    'temp_max'      => $daily['temperature_2m_max'][$index],
                    'temp_min'      => $daily['temperature_2m_min'][$index],
                    'precipitation' => $daily['precipitation_sum'][$index],
                    'weather_code'  => $code,
                    'icon'          => $this->getWeatherIcon($code),
                    'description'   => $this->getWeatherDescription($code),
                ],
            );
        }
    }

    public function getWeatherHistoryFromDb(string $city): array
    {
        return WeatherHistory::where('city', $city)
            ->where('date', '>=', now()->subDays(7)->toDateString())
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();
    }

    private function getCoordinates(string $city, ?string $state = null): ?array
    {
        $query = $city;

        if ($state) {
            $query .= ", {$state}";
        }

        $location = $this->fetchCoordinates($query);

        if (! $location && $state) {
            $location = $this->fetchCoordinates($city);
        }

        return $location;
    }

    private function fetchCoordinates(string $query): ?array
    {
        $response = Http::get(self::GEOCODING_URL, [
            'name'     => $query,
            'count'    => 1,
            'language' => 'pt',
            'format'   => 'json',
        ]);

        if ($response->successful() && ! empty($response->json('results'))) {
            $result = $response->json('results')[0];

            return [
                'latitude'  => $result['latitude'],
                'longitude' => $result['longitude'],
            ];
        }

        return null;
    }

    public function getWeatherIcon(int $code): string
    {
        return match (true) {
            $code === 0                   => 'sun',
            in_array($code, [1, 2, 3])    => 'cloud',
            in_array($code, [45, 48])     => 'eye-slash',
            in_array($code, [51, 53, 55]) => 'cloud',
            in_array($code, [61, 63, 65]) => 'cloud',
            in_array($code, [71, 73, 75]) => 'variable',
            in_array($code, [80, 81, 82]) => 'cloud',
            $code >= 95                   => 'bolt',
            default                       => 'cloud',
        };
    }

    public function getWeatherDescription(int $code): string
    {
        return match (true) {
            $code === 0                   => __('dashboard.weather.clear_sky'),
            $code === 1                   => __('dashboard.weather.mainly_clear'),
            $code === 2                   => __('dashboard.weather.partly_cloudy'),
            $code === 3                   => __('dashboard.weather.overcast'),
            in_array($code, [45, 48])     => __('dashboard.weather.fog'),
            in_array($code, [51, 53, 55]) => __('dashboard.weather.drizzle'),
            in_array($code, [61, 63, 65]) => __('dashboard.weather.rain'),
            in_array($code, [71, 73, 75]) => __('dashboard.weather.snow'),
            in_array($code, [80, 81, 82]) => __('dashboard.weather.showers'),
            $code >= 95                   => __('dashboard.weather.thunderstorm'),
            default                       => __('dashboard.weather.unknown'),
        };
    }
}
