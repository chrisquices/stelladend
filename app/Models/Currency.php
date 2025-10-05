<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'currencies';

    protected $guarded = ['id'];

    public function getFlagUrlAttribute() {
        return config('app.url') . '/storage/currencies/' . $this->code . '.svg';
    }
}
