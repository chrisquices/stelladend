<?php

namespace App\Http\Controllers;

use App\Helpers\CalendarHelper;
use App\Http\Requests\Calendar\IndexCalendarRequest;
use App\Http\Requests\Calendar\TogglePortfolioInclusionRequest;
use App\Http\Requests\Calendar\ToggleWatchlistInclusionRequest;
use App\Models\Portfolio;
use App\Models\Watchlist;
use App\Services\CalendarService;
use Inertia\Inertia;

class CalendarController extends Controller
{
    private $calendarService;

    public function __construct()
    {
        $this->calendarService = new CalendarService();
    }

    public function index(IndexCalendarRequest $request)
    {
        $periods = CalendarHelper::generatePeriods();

        if ($request->input('period')) {
            $selectedPeriod = collect($periods)->where('period', $request->input('period'))->first();
        } else {
            $selectedPeriod = collect($periods)->where('isCurrentPeriod', true)->first();
        }

        $calendarDays = $this->calendarService->getCalendarEvents($selectedPeriod['period']);

        $portfolios = $this->calendarService->getPortfolios();
        $watchlists = $this->calendarService->getWatchlists();

        return Inertia::render('calendar/Index', [
            'periods'      => $periods,
            'calendarDays' => $calendarDays,
            'portfolios'   => $portfolios,
            'watchlists'   => $watchlists,
        ]);
    }

    public function togglePortfolioInclusion(TogglePortfolioInclusionRequest $request)
    {
        $portfolio = Portfolio::where('uid', $request->portfolioUid)->first();

        $this->calendarService->togglePortfolioInclusion($portfolio->id);

        return back();
    }

    public function toggleWatchlistInclusion(ToggleWatchlistInclusionRequest $request)
    {
        $watchlist = Watchlist::where('uid', $request->watchlistUid)->first();

        $this->calendarService->toggleWatchlistInclusion($watchlist->id);

        return back();
    }
}
