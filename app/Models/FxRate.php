<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FxRate extends Model
{
    protected $table = 'fx_rates';

    protected $guarded = ['id'];

    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}
