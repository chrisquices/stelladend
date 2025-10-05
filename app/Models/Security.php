<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Security extends Model
{
    protected $table = 'securities';

    protected $guarded = ['id'];

    public function exchanges()
    {
        return $this->belongsTo(Exchange::class);
    }

    public function getLogoUrlAttribute() {
        if ($this->has_logo) {
            return config('app.url') . '/storage/securities/' . $this->ticker . '.png';
        }

        return null;
    }

    public function getDividendPayoutFrequencyFormattedAttribute()
    {
        switch ($this->dividend_payout_frequency) {
            case 12:
                return 'Monthly';
            case 6:
                return 'Bi-Monthly';
            case 4:
                return 'Quarterly';
            case 2:
                return 'Semi-Annual';
            case 1:
                return 'Annual';
            default:
                return null;
        }
    }
}
