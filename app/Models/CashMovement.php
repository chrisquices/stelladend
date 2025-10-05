<?php

namespace App\Models;

use App\Helpers\CashMovementHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashMovement extends Model
{
    use SoftDeletes;

    protected $table = 'cash_movements';

    protected $guarded = ['id'];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function dividendReceived()
    {
        return $this->belongsTo(DividendReceived::class);
    }

    public function getTypeFormattedAttribute()
    {
        return CashMovementHelper::formatType($this->type);
    }
}
