<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Interfaces\CurrencyRateService;
use App\Http\Services\FreeCurrencyAPIService;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        $this->app->bind(CurrencyRateService::class, FreeCurrencyAPIService::class);
    }

    public function boot(): void
    {
    }
}
