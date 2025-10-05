<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dividend extends Model
{
    protected $table = 'dividends';

    protected $guarded = ['id'];

    public function security()
    {
        return $this->belongsTo(Security::class);
    }
}
