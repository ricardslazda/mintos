<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Http\Interfaces\CurrencyRateService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FreeCurrencyAPIService implements CurrencyRateService
{
    const CACHE_TTL = 60;

    public function getCurrencyRatesForUSD(): array
    {
        return Cache::remember('currency_rates', self::CACHE_TTL, function () {
            try {
                $response = Http::get(env('FREE_CURRENCY_API_URL'), ['apikey' => env('FREE_CURRENCY_API_KEY')]);
                if ($response->successful()) {
                    return $response['data'];
                } else {
                    throw new Exception('Failed to fetch currency rates.');
                }
            } catch (Exception $e) {
                throw new Exception('Currency API unavailable: ' . $e->getMessage());
            }
        });
    }
}
