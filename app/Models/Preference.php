<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    protected $table = 'preferences';

    protected $guarded = ['id'];

    public function secondaryCurrency()
    {
        return $this->belongsTo(Currency::class, 'secondary_currency_id');
    }
}
