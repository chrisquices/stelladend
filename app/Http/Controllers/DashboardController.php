<?php

namespace App\Http\Controllers;

use App\Helpers\DashboardHelper;
use App\Models\MarketDate;
use App\Models\MarketQuote;
use App\Services\DashboardService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    private $dashboardService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
    }

    public function index()
    {
        $showTutorial = true;
        $portfolios = $this->dashboardService->getPortfolios();
        $portfoliosAndAccounts = $this->dashboardService->getPortfoliosAndAccounts($portfolios);
        $accountStats = DashboardHelper::calculateAccountStats($portfolios, $portfoliosAndAccounts);
        $hasBeenWelcomed = $this->dashboardService->getHasBeenWelcomed();
        $hasSecondaryCurrency = $this->dashboardService->hasSecondaryCurrency();

        return Inertia::render('dashboard/Index', [
            'showTutorial'          => $showTutorial,
            'portfolios'            => $portfolios,
            'portfoliosAndAccounts' => $portfoliosAndAccounts,
            'accountStats'          => $accountStats,
            'hasBeenWelcomed'       => $hasBeenWelcomed,
            'hasSecondaryCurrency'  => $hasSecondaryCurrency
        ]);
    }

    public function ogImage()
    {
        return Inertia::render('dashboard/OgImage');
    }
}
