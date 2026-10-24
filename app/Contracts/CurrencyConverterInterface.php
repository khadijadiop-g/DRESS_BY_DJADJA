<?php

namespace App\Contracts;

interface CurrencyConverterInterface
{
    /**
     * Convert an amount in FCFA (XOF) to EUR (€)
     */
    public function xofToEur(int|float $amountXof): float;

    /**
     * Convert an amount in EUR (€) to FCFA (XOF)
     */
    public function eurToXof(float $amountEur): int;

    /**
     * Get the active conversion rate (1 EUR = X FCFA)
     */
    public function getRate(): float;
}
