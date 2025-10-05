<?php

namespace App\Helpers;

use App\Http\Controllers\Controller;

class AccountHelper extends Controller
{
    public static function convertToSecondary(float $balanceInUSD, string $originalCurrency, $secondaryCurrency, $fxRates, string $type = 'account') {
        if (!$secondaryCurrency) return null;

        $toCurrency = $secondaryCurrency->code;

        if ($toCurrency === $originalCurrency) {
            $value = $balanceInUSD * ($fxRates[$originalCurrency] ?? 1); // reverse USD conversion
        } elseif ($toCurrency === 'USD') {
            $value = $balanceInUSD;
        } elseif (isset($fxRates[$toCurrency])) {
            $value = $balanceInUSD * $fxRates[$toCurrency];
        } else {
            $value = 0;
        }

        $formatted = NumberHelper::formatNumberToDollar($value, $secondaryCurrency);

        return match ($type) {
            'account_movement' => [
                'currencyCode'    => $toCurrency,
                'amount'          => $value,
                'amountFormatted' => $formatted,
            ],
            default => [
                'currencyCode'    => $toCurrency,
                'balance'         => $value,
                'balanceFormatted'=> $formatted,
            ]
        };
    }
}
