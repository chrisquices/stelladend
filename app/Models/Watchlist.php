<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    protected $table = 'watchlists';

    protected $guarded = ['id'];

    public function watchlistSecurities()
    {
        return $this->hasMany(WatchlistSecurity::class);
    }
}
