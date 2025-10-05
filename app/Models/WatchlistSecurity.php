<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchlistSecurity extends Model
{
    protected $table = 'watchlist_securities';

    protected $guarded = ['id'];

    public function watchlist()
    {
        return $this->belongsTo(Watchlist::class);
    }

    public function security()
    {
        return $this->belongsTo(Security::class);
    }
}
