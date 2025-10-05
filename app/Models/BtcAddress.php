<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BtcAddress extends Model
{
    protected $table = 'btc_addresses';

    protected $guarded = ['id'];
}
