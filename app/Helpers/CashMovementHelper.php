<?php

namespace App\Helpers;

class CashMovementHelper
{
    public static function matchType($type)
    {
        $deposit = 'deposit';
        $withdrawal = 'withdrawal';
        $securityBuy = 'security_buy';
        $securityBuyDrip = 'security_buy_drip';
        $securitySell = 'security_sell';
        $dividend = 'dividend_received';
        $interest = 'interest';
        $other = 'other';

        return match ($type) {
            // System types
            'deposit' => $deposit,
            'withdrawal' => $withdrawal,
            'security_buy' => $securityBuy,
            'security_buy_drip' => $securityBuyDrip,
            'security_sell' => $securitySell,
            'dividend' => $dividend,
            'interest' => $interest,
            'other' => $other,

            // Schwab types
            'buy' => $securityBuy,
            'buy_drip' => $securityBuyDrip,
            'sell' => $securitySell,
            'wire received' => $deposit,
            'funds received' => $deposit,
            'atm withdrawal' => $withdrawal,
            'schwab atm rebate' => $deposit,
            'credit interest' => $interest,
            'reinvest dividend' => $dividend,
            'reinvest shares' => $securityBuyDrip,


            default => 'Unknown',
        };
    }

    public static function formatType($type)
    {
        $deposit = 'Deposit';
        $withdrawal = 'Withdrawal';
        $securityBuy = 'Security Buy';
        $securityBuyDrip = 'Security Buy (DRIP)';
        $securitySell = 'Security Sell';
        $dividend = 'Dividend Received';
        $interest = 'Interest';
        $other = 'Other';

        return match ($type) {
            // System types
            'deposit' => $deposit,
            'withdrawal' => $withdrawal,
            'security_buy' => $securityBuy,
            'security_buy_drip' => $securityBuyDrip,
            'security_sell' => $securitySell,
            'dividend_received' => $dividend,
            'interest' => $interest,
            'other' => $other,

            default => 'Unknown',
        };
    }
}
