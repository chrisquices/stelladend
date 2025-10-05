<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $table = 'transactions';

    protected $guarded = ['id'];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function security()
    {
        return $this->belongsTo(Security::class);
    }

    public function getTypeFormattedAttribute()
    {
        return match ($this->type) {
            'buy' => 'Buy',
            'buy_drip' => 'Buy (DRIP)',
            'sell' => 'Sell',
            default => ucfirst($this->type),
        };
    }
}
