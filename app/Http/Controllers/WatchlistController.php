<?php

namespace App\Http\Controllers;

use App\Helpers\NumberHelper;
use App\Helpers\WatchlistHelper;
use App\Http\Requests\Watchlists\StoreWatchlistRequest;
use App\Http\Requests\Watchlists\UpdateWatchlistRequest;
use App\Models\Security;
use App\Models\Watchlist;
use App\Services\WatchlistService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WatchlistController extends Controller
{
    use AuthorizesRequests;

    private $watchlistService;

    public function __construct()
    {
        $this->watchlistService = new WatchlistService();
    }

    public function index()
    {
        $this->watchlistService->storeStarterWatchlist();

        $defaultWatchlist = $this->watchlistService->getDefaultWatchlist();

        return to_route('watchlists.show', ['watchlistUid' => $defaultWatchlist->uid]);
    }

    public function store(StoreWatchlistRequest $request)
    {
        $watchlist = $this->watchlistService->store($request->validated());

        return to_route('watchlists.show', ['watchlistUid' => $watchlist->uid]);
    }

    public function show(Request $request, $watchlistUid)
    {
        $filters = [
            'sortBy'        => $request->sortBy ?? 'ticker',
            'sortDirection' => $request->sortDirection ?? 'asc',
        ];

        $watchlist = $this->watchlistService->getWatchlistByUid($watchlistUid);

        $this->authorize('view', $watchlist);

        $watchlists = $this->watchlistService->getWatchlists();
        $selectedWatchlist = $this->watchlistService->getWatchlist($watchlist->id, $filters['sortBy'], $filters['sortDirection']);
         $metrics = WatchlistHelper::calculateMetrics($selectedWatchlist['watchlistSecurities']);

        return Inertia::render('watchlists/Show', [
            'selectedWatchlist' => $selectedWatchlist,
            'metrics'           => $metrics,
            'watchlists'        => $watchlists,
            'filters'           => $filters,
        ]);
    }

    public function update(UpdateWatchlistRequest $request, $watchlistUid)
    {
        $watchlist = Watchlist::where('uid', $watchlistUid)->first();

        $watchlist = $this->watchlistService->update($watchlist->id, $request->validated());

        return to_route('watchlists.show', ['watchlistUid' => $watchlist->uid]);
    }

    public function delete($watchlistUid)
    {
        $watchlist = Watchlist::where('uid', $watchlistUid)->first();

        $this->watchlistService->delete($watchlist->id);

        return to_route('watchlists.index');
    }

    public function getSecurities(Request $request)
    {
        if ($request->search) {
            return Security::query()
                ->where('ticker', 'ilike', "%$request->search%")
                ->orderByRaw("
                    CASE
                        WHEN ticker ilike ? THEN 0      -- Exact match
                        WHEN ticker ilike ? THEN 1      -- Prefix match
                        ELSE 2                          -- Anything else
                    END, ticker
                ", [
                    $request->search,        // exact match
                    $request->search . '%',  // prefix match
                ])
                ->limit(50)
                ->get()
                ->map(function ($item) {
                    return [
                        'uid'     => $item->uid,
                        'name'    => $item->name,
                        'ticker'  => $item->ticker,
                        'logoUrl' => $item->logoUrl,
                        'color'   => $item->color,
                    ];
                });
        }

        return [];
    }

    public function sortWatchlistSecurities(Request $request)
    {
        $filters = [
            'sortBy'        => $request->sortBy ?? 'ticker',
            'sortDirection' => $request->sortDirection ?? 'desc',
        ];

        $watchlistSecurities = $request->watchlistSecurities ?? [];

        return WatchlistHelper::sortHoldings($watchlistSecurities, $filters['sortBy'], $filters['sortDirection']);
    }

    public function export(Request $request)
    {
        $watchlist = Watchlist::where('uid', $request->watchlistUid)->first();

        $exportedWatchlist = $this->watchlistService->export($watchlist->id);

        return $exportedWatchlist;
    }
}
