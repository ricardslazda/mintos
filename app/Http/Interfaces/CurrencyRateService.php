<?php

declare(strict_types=1);

namespace App\Http\Interfaces;

interface CurrencyRateService
{
    /**
     * @return String[]
     */
    public function getCurrencyRatesForUSD(): array;
}
