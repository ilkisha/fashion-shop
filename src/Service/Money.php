<?php

namespace App\Service;

final class Money
{
    /** Converts "129.99" (string/decimal) to 12999 (int cents) */
    public static function eurToCents(string $amount): int
    {
        $normalized = str_replace(',', '.', trim($amount));

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $normalized)) {
            throw new \InvalidArgumentException('Invalid money format: '.$amount);
        }

        [$euros, $cents] = array_pad(explode('.', $normalized, 2), 2, '0');
        $cents = str_pad($cents, 2, '0');

        return ((int)$euros * 100) + (int)$cents;
    }
}
