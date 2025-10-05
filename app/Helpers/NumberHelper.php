<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Number;

class NumberHelper
{
    public static function formatNumberWithSeparators($number, $decimals = 0)
    {
        $numberFormatted = number_format($number, $decimals, '.', ',');

        return $numberFormatted;
    }

    public static function formatNumberToDollar($number, $secondaryCurrency = null)
    {
        // Set defaults or use secondaryCurrency values
        $thousandsSeparator = $secondaryCurrency->thousands_separator ?? ',';
        $decimal_separator = $secondaryCurrency->decimal_separator ?? '.';
        $decimalPoints = $secondaryCurrency->decimal_points ?? 2;
        $symbol = $secondaryCurrency->symbol ?? '$';

        // Check if the number is negative
        $isNegative = $number < 0;

        // Convert the number to a positive one for formatting
        $number = abs($number);

        // Format the number with the specified number of decimal places, adding trailing zeros if necessary
        $formattedNumber = number_format($number, $decimalPoints, $decimal_separator, $thousandsSeparator);

        // Prefix with the currency symbol and append the minus sign if needed
        return ($isNegative ? '-' . $symbol : $symbol) . $formattedNumber;
    }

    public static function formatNumberToPercentage($number, $maxSignificantDecimal = 2)
    {
        if (!$number) {
            return Number::percentage(0, precision: $maxSignificantDecimal);
        }

        return Number::percentage($number, precision: $maxSignificantDecimal);
    }

    public static function formatNumberToPERatio($number)
    {
        // Truncate the number to 2 decimal places without rounding
        $numberTruncated = floor($number * 100) / 100;

        // Format the truncated number with exactly 2 decimal places
        $numberFormatted = number_format($numberTruncated, 2, '.', ',');
        $symbol = 'x';

        return "{$numberFormatted}{$symbol}";
    }

    public static function countDecimals($number): int
    {
        if ((int)$number == $number) {
            return 0;
        }

        $decimalStr = rtrim(substr(strrchr((string)$number, '.'), 1), '0');

        return strlen($decimalStr);
    }
}
