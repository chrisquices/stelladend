<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\FxRate;
use App\Models\MarketDate;

class FxRateService
{
    public function getLatestFxRate($currencyId = null, $currencyCode = null)
    {
        $latestMarketDate = MarketDate::latest('date')->first();

        if ($currencyId) {
            $fxRate = FxRate::query()
                ->where('to_currency_id', $currencyId)
                ->where('date', $latestMarketDate->date)
                ->first();

            if (!$fxRate) {
                $fxRate = FxRate::query()
                    ->where('to_currency_id', $currencyId)
                    ->latest('date')
                    ->first();
            }

            return $fxRate;
        }

        if ($currencyCode) {
            $currency = Currency::select('id')->where('code', $currencyCode)->first();

            $fxRate = FxRate::query()
                ->where('to_currency_id', $currency->id)
                ->where('date', $latestMarketDate->date)
                ->first();

            if (!$fxRate) {
                $fxRate = FxRate::query()
                    ->where('to_currency_id', $currency->id)
                    ->latest('date')
                    ->first();
            }

            return $fxRate;
        }
    }

    public function getLatestFxRates()
    {
        $latestFxDate = FxRate::max('date');

        return FxRate::query()
            ->with('toCurrency')
            ->whereDate('date', $latestFxDate)
            ->get()
            ->map(function ($fxRate) {
                return [
                    'to_currency' => $fxRate->toCurrency->code,
                    'rate'        => $fxRate->rate,
                    'date'        => $fxRate->date,
                ];
            });
    }

    public function getLatestFxRatesBy()
    {
        $latestMarketDate = MarketDate::latest('date')->first();

        return FxRate::query()
            ->with('toCurrency')
            ->where('date', '<=', $latestMarketDate->date)
            ->get()
            ->map(function ($fxRate) {
                return [
                    'to_currency' => $fxRate->toCurrency->code,
                    'rate'        => $fxRate->rate,
                    'date'        => $fxRate->date,
                ];
            });
    }
}
