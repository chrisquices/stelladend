<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\FxRate;
use App\Models\MarketDate;
use App\Models\Preference;

class PreferenceService
{
    public function update($data)
    {
        $user = auth()->user();
        $preferences = $user->preferences;

        $preferences->update([
            $data['key'] => $data['value'] ?? null
        ]);

        return $preferences;
    }

    public function storeStarterPreferences()
    {
        $user = auth()->user();

        Preference::insert([
            'user_id'               => $user->id,
            'secondary_currency_id' => null,
            'theme'                 => 'emerald-light',
            'has_been_welcomed'     => false
        ]);
    }
}
