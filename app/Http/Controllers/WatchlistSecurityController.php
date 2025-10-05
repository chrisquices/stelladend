<?php

namespace App\Http\Controllers;

use App\Http\Requests\WatchlistSecurities\StoreWatchlistSecurityRequest;
use App\Models\Security;
use App\Models\Watchlist;
use App\Services\WatchlistSecurityService;

class WatchlistSecurityController extends Controller
{
    private $watchlistSecurityService;

    public function __construct()
    {
        $this->watchlistSecurityService = new WatchlistSecurityService();
    }

    public function store(StoreWatchlistSecurityRequest $request, $watchlistUid)
    {
        $watchlist = Watchlist::where('uid', $watchlistUid)->first();
        $security = Security::where('uid', $request->securityUid)->first();

        $this->watchlistSecurityService->store($watchlist->id, $security->id);

        return back();
    }

    public function delete($watchlistUid, $securityUid)
    {
        $watchlist = Watchlist::where('uid', $watchlistUid)->first();
        $security = Security::where('uid', $securityUid)->first();

        $this->watchlistSecurityService->delete($watchlist->id, $security->id);

        return back();
    }
}
