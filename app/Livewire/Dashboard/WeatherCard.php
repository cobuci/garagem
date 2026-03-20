<?php

namespace App\Livewire\Dashboard;

use App\Models\Setting;
use App\Models\WeatherHistory;
use App\Services\WeatherService;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class WeatherCard extends Component
{
    public ?array $weather = null;

    public bool $hasError = false;

    public function mount(): void
    {
        $this->loadWeather();
    }

    public function loadWeather(bool $force = false): void
    {
        $this->hasError = false;
        $city = Setting::singleton()->city;

        if (empty($city)) {
            $this->weather = null;

            return;
        }

        $latestHistory = WeatherHistory::where('city', $city)
            ->whereNotNull('temp_current')
            ->where('updated_at', '>=', now()->subHour())
            ->latest()
            ->first();

        if (! $force && $latestHistory) {
            $this->weather = [
                'current' => [
                    'temperature_2m' => $latestHistory->temp_current,
                    'weather_code'   => $latestHistory->weather_code,
                    'city'           => $city,
                    'time'           => $latestHistory->updated_at->toDateTimeString(),
                ],
                'history' => app(WeatherService::class)->getWeatherHistoryFromDb($city),
            ];

            return;
        }

        $this->weather = app(WeatherService::class)->getCurrentWeather();

        if ($this->weather === null) {
            $this->hasError = true;
        }
    }

    #[Computed]
    public function current(): ?array
    {
        return $this->weather['current'] ?? null;
    }

    #[Computed]
    public function history(): array
    {
        return $this->weather['history'] ?? [];
    }

    #[Computed]
    public function lastUpdated(): string
    {
        if ($this->weather === null) {
            return '';
        }

        return now()->parse($this->current()['time'] ?? now())->diffForHumans();
    }

    #[Computed]
    public function icon(): string
    {
        if ($this->current() === null) {
            return 'cloud';
        }

        return app(WeatherService::class)->getWeatherIcon($this->current()['weather_code']);
    }

    #[Computed]
    public function description(): string
    {
        if ($this->current() === null) {
            return '';
        }

        return app(WeatherService::class)->getWeatherDescription($this->current()['weather_code']);
    }

    public function render(): View
    {
        return view('livewire.dashboard.weather-card');
    }

    public function placeholder(): View
    {
        return view('livewire.dashboard.weather-card-placeholder');
    }
}
