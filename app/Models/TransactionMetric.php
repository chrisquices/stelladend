<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionMetric extends Model
{
    protected $table = 'transaction_metrics';

    protected $guarded = ['id'];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function security()
    {
        return $this->belongsTo(Security::class);
    }
}
