<?php

namespace App\Services\Currency;

use App\Contracts\CurrencyConverterInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class CurrencyConverterService implements CurrencyConverterInterface
{
    public const DEFAULT_RATE = 655.957;

    public function getRate(): float
    {
        return (float) Cache::remember('currency_rate_eur_xof', 86400, function () {
            return Setting::get('currency_rate_eur_xof', (string) self::DEFAULT_RATE);
        });
    }

    public function xofToEur(int|float $amountXof): float
    {
        $rate = $this->getRate();
        return round($amountXof / $rate, 2);
    }

    public function eurToXof(float $amountEur): int
    {
        $rate = $this->getRate();
        return (int) round($amountEur * $rate);
    }
}
