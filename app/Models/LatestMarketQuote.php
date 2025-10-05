<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LatestMarketQuote extends Model
{
    protected $table = 'latest_market_quotes';

    protected $guarded = ['id'];

    public function security()
    {
        return $this->belongsTo(Security::class);
    }
}
