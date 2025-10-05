<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DividendReceived extends Model
{
    protected $table = 'dividends_received';

    protected $guarded = ['id'];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function security()
    {
        return $this->belongsTo(Security::class);
    }

    public function dividend()
    {
        return $this->belongsTo(Dividend::class);
    }
}
