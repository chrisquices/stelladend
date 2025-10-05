<?php

namespace App\Services;

use App\Helpers\PortfolioMetricHelper;
use App\Models\PortfolioMetric;

class PortfolioMetricService
{
    public function getPortfolioMetrics($portfolioId, $holdings)
    {
        $portfolioMetric = PortfolioMetric::query()
            ->where('portfolio_id', $portfolioId)
            ->first();

        return PortfolioMetricHelper::formatPortfolioMetric($portfolioMetric, $holdings);
    }
}
