<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketQuote extends Model
{
    protected $table = 'market_quotes';

    protected $guarded = ['id'];

    public function security()
    {
        return $this->belongsTo(Security::class);
    }
}
