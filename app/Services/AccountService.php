<?php

namespace App\Services;

use App\Helpers\NumberHelper;
use App\Models\Currency;
use App\Models\Account;
use Illuminate\Support\Str;

class AccountService
{
    public function getAccounts()
    {
        $user = auth()->user();
        $secondaryCurrency = $user->preferences->secondaryCurrency;

        $fxRates = app(FxRateService::class)->getLatestFxRates();

        return Account::query()
            ->with('currency')
            ->where('user_id', $user->id)
            ->orderBy('order', 'asc')
            ->get()
            ->map(function ($account) use ($fxRates, $secondaryCurrency) {
                // Primary Currency
                if ($account->currency->code === 'USD') {
                    $balance = $account->balance;
                    $balanceFormatted = NumberHelper::formatNumberToDollar($balance);
                } else {
                    $relevantFxRate = $fxRates->where('to_currency', $account->currency->code)->first();

                    $balance = $account->balance / $relevantFxRate['rate'];
                    $balanceFormatted = NumberHelper::formatNumberToDollar($balance);
                }

                // Secondary Currency
                if ($secondaryCurrency && $account->currency->code === 'USD') {
                    $relevantFxRate = $fxRates->where('to_currency', $secondaryCurrency->code)->first();

                    $secondaryCurrencyData = [
                        'currencyCode'     => $secondaryCurrency->code,
                        'balance'          => $account->balance * $relevantFxRate['rate'],
                        'balanceFormatted' => NumberHelper::formatNumberToDollar($account->balance * $relevantFxRate['rate'], $secondaryCurrency),
                    ];
                } else {
                    $secondaryCurrencyData = [
                        'currencyCode'     => $account->currency->code,
                        'balance'          => floatval($account->balance),
                        'balanceFormatted' => NumberHelper::formatNumberToDollar($account->balance, $secondaryCurrency),
                    ];
                }

                if ($account->currency->code === 'USD') $balanceToDisplayInClient = $balance;
                if ($account->currency->code !== 'USD') $balanceToDisplayInClient = $secondaryCurrencyData['balance'];

                return [
                    'currencyId'               => $account->currency_id,
                    'uid'                      => $account->uid,
                    'name'                     => $account->name,
                    'reference'                => $account->reference,
                    'balance'                  => $balance,
                    'balanceFormatted'         => $balanceFormatted,
                    'balanceToDisplayInClient' => $balanceToDisplayInClient,
                    ...($secondaryCurrencyData ? ['secondaryCurrency' => $secondaryCurrencyData] : [])
                ];
            });
    }

    public function getCurrencies()
    {
        $user = auth()->user();

        $availableCurrencies = ['USD'];

        $secondaryCurrency = $user->preferences->secondaryCurrency;

        if ($secondaryCurrency) {
            $availableCurrencies[] = $secondaryCurrency->code;
        }
        return Currency::query()
            ->whereIn('code', $availableCurrencies)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($category) {
                return [
                    'id'     => $category->id,
                    'code'   => $category->code,
                    'symbol' => $category->symbol,
                ];
            });
    }

    public function store()
    {
        $user = auth()->user();

        $accounts = Account::query()
            ->where('user_id', $user->id)
            ->count();

        $order = $accounts + 1;

        $account = Account::create([
            'uid'         => Str::uuid()->toString(),
            'user_id'     => $user->id,
            'currency_id' => 1,
            'name'        => 'My Savings Account',
            'reference'   => rand(10000000, 99999999),
            'balance'     => 0,
            'order'       => $order,
        ]);

        $this->resetOrders();

        return $account;
    }

    public function update($accountId, $accountData)
    {
        $account = Account::findOrFail($accountId);

        $account->update([
            'currency_id' => $accountData['currencyId'] ?? 1,
            'name'        => $accountData['name'] ?? 'My Savings Account',
            'reference'   => $accountData['reference'] ?? '0',
            'balance'     => $accountData['balance'] ?? '0',
        ]);

        $this->resetOrders();

        return $account;
    }

    public function delete($accountId)
    {
        $account = Account::findOrFail($accountId);

        $account->delete();

        $this->resetOrders();
    }

    public function resetOrders()
    {
        $user = auth()->user();

        $accounts = Account::query()
            ->where('user_id', $user->id)
            ->orderBy('order', 'asc')
            ->get();

        $newOrder = 1;

        foreach ($accounts as $account) {
            $account->update([
                'order' => $newOrder,
            ]);

            $newOrder++;
        }
    }
}
