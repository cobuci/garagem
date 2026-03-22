<?php

namespace App\Providers;

use App\Models\Setting;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureAppName();
        $this->configureMail();

        Model::unguard();

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }

    protected function configureMail(): void
    {
        config(['mail.markdown.theme' => 'garagem']);
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function configureAppName(): void
    {
        try {
            $storeName = Cache::remember('store_name', 3600, function () {
                return Setting::first()?->store_name;
            });

            if ($storeName) {
                config(['app.name' => $storeName]);
            }
        } catch (\Throwable $e) {
        }
    }
}
