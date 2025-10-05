<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioMetric extends Model
{
    protected $table = 'portfolio_metrics';

    protected $guarded = ['id'];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}
