<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Hash;

class SettingService
{
    public function getUser()
    {
        $user = auth()->user();

        return [
            'email' => $user->email,
        ];
    }

    public function getCurrencies()
    {
        return Currency::query()
            ->whereNot('code', 'USD') // USD is already the base currency, no need to have it as a secondary currency option
            ->get()
            ->map(function ($currency) {
                return [
                    'id'      => $currency->id,
                    'code'    => $currency->code,
                    'flagUrl' => $currency->flagUrl,
                ];
            });
    }

    public function getSecondaryCurrency()
    {
        $user = auth()->user();
        $preferences = $user->preferences;

        $secondaryCurrency = Currency::query()
            ->where('id', $preferences->secondary_currency_id)
            ->first();

        if ($secondaryCurrency) {
            return [
                'id'      => $secondaryCurrency->id,
                'code'    => $secondaryCurrency->code,
                'flagUrl' => $secondaryCurrency->flagUrl,
            ];
        }

        return [];
    }

    public function getTheme() {
        return auth()->user()->preferences->theme;
    }

    public function updateAccountSettings($data)
    {
        $user = auth()->user();

        $user->update([
            'email' => $data['email'],
        ]);

        return $user;
    }

    public function updatePassword($data)
    {
        $user = auth()->user();

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }

}
