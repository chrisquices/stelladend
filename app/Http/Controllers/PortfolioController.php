<?php

namespace App\Http\Controllers;

use App\Helpers\NumberHelper;
use App\Helpers\PortfolioHelper;
use App\Http\Requests\Portfolios\ComparePortfolioRequest;
use App\Http\Requests\Portfolios\StorePortfolioRequest;
use App\Http\Requests\Portfolios\UpdateHoldingRequest;
use App\Http\Requests\Portfolios\UpdatePortfolioRequest;
use App\Models\CashMovement;
use App\Models\Currency;
use App\Models\LatestMarketQuote;
use App\Models\Portfolio;
use App\Models\Security;
use App\Services\PortfolioService;
use App\Services\TransactionMetricService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PortfolioController extends Controller
{
    use AuthorizesRequests;

    private $portfolioService;

    public function __construct()
    {
        $this->portfolioService = new PortfolioService();
    }

    public function index()
    {
        $this->portfolioService->storeStarterPortfolio();

        $defaultPortfolio = $this->portfolioService->getDefaultPortfolio();

        return to_route('portfolios.show', ['portfolioUid' => $defaultPortfolio->uid]);
    }

    public function store(StorePortfolioRequest $request)
    {
        $portfolio = $this->portfolioService->store($request->validated());

        return to_route('portfolios.show', ['portfolioUid' => $portfolio->uid]);
    }

    public function show($portfolioUid, Request $request)
    {
        $performanceRange = $request->ppr ?? 'ytd';

        $portfolio = $this->portfolioService->getPortfolioByUid($portfolioUid);

        $this->authorize('view', $portfolio);

//        $this->portfolioService->recalculate($portfolio->id); // TODO delete later

        $portfolios = $this->portfolioService->getPortfolios();
        $selectedPortfolio = $this->portfolioService->getPortfolio($portfolio->id);
        $performance = $this->portfolioService->getPortfolioPerformance($portfolio->id, $performanceRange);
        $thisYearsDividends = $this->portfolioService->getThisYearsDividends($portfolio->id, $selectedPortfolio['holdings']);
        $upcomingDividends = $this->portfolioService->getUpcomingDividends($portfolio->id);
        $dividendsReceived = $this->portfolioService->getDividendsReceived($portfolio->id);
        $cashMovements = $this->portfolioService->getCashMovements($portfolio->id);
        $secondaryCurrency = $this->portfolioService->getSecondaryCurrency();

        return Inertia::render('portfolios/Show', [
            'portfolios'         => $portfolios,
            'selectedPortfolio'  => $selectedPortfolio,
            'performance' => $performance,
            'thisYearsDividends' => $thisYearsDividends,
            'upcomingDividends'  => $upcomingDividends,
            'dividendsReceived'  => $dividendsReceived,
            'cashMovements'      => $cashMovements,
            'secondaryCurrency'   => $secondaryCurrency,
        ]);
    }

    public function showPublic($publicReferenceCode)
    {
        $portfolio = Portfolio::where('public_reference_code', $publicReferenceCode)->firstOrFail();

        if (!$portfolio->is_public) {
            abort(404);
        }

        $selectedPortfolio = $this->portfolioService->getPortfolio($portfolio->id);
        $thisYearsDividends = $this->portfolioService->getThisYearsDividends($portfolio->id, $selectedPortfolio['holdings']);

        $isLoggedIn = auth()->check();

        return Inertia::render('portfolios/ShowPublic', [
            'selectedPortfolio'  => $selectedPortfolio,
            'thisYearsDividends' => $thisYearsDividends,
            'isLoggedIn'         => $isLoggedIn,
        ]);
    }

    public function update(UpdatePortfolioRequest $request, $portfolioUid)
    {
        $portfolio = Portfolio::where('uid', $portfolioUid)->first();

        $portfolio = $this->portfolioService->update($portfolio->id, $request->validated());

        return to_route('portfolios.show', ['portfolioUid' => $portfolio->uid]);
    }

    public function switchMode($portfolioUid)
    {
        $portfolio = Portfolio::where('uid', $portfolioUid)->first();

        $portfolio = $this->portfolioService->switchMode($portfolio->id);

        return to_route('portfolios.show', ['portfolioUid' => $portfolio->uid]);
    }

    public function wipe($portfolioUid)
    {
        $portfolio = Portfolio::where('uid', $portfolioUid)->first();

        $portfolio = $this->portfolioService->wipe($portfolio->id);

        return to_route('portfolios.show', ['portfolioUid' => $portfolio->uid]);
    }

    public function delete($portfolioUid)
    {
        $portfolio = Portfolio::where('uid', $portfolioUid)->first();

        $this->portfolioService->delete($portfolio->id);

        return to_route('portfolios.index');
    }

    public function getSecurities(Request $request)
    {
        if ($request->search) {
            $latestMarketQuotes = LatestMarketQuote::select('security_id', 'close')->get()->keyBy('security_id');

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
                ->map(function ($item) use ($latestMarketQuotes) {
                    $currentPrice = $latestMarketQuotes[$item->id]['close'];

                    return [
                        'uid'                   => $item->uid,
                        'name'                  => $item->name,
                        'ticker'                => $item->ticker,
                        'logoUrl'               => $item->logoUrl,
                        'color'                 => $item->color,
                        'currentPrice'          => floatval($currentPrice),
                        'currentPriceFormatted' => NumberHelper::formatNumberToDollar($currentPrice),
                    ];
                });
        }

        return [];
    }

    public function sortHoldings(Request $request)
    {
        $filters = [
            'sortBy'        => $request->sortBy ?? 'ticker',
            'sortDirection' => $request->sortDirection ?? 'desc',
        ];

        $holdings = $request->holdings ?? [];

        return PortfolioHelper::sortHoldings($holdings, $filters['sortBy'], $filters['sortDirection']);
    }

    public function sortUpcomingDividends(Request $request)
    {
        $filters = [
            'sortBy'        => $request->sortBy ?? 'ticker',
            'sortDirection' => $request->sortDirection ?? 'desc',
        ];

        $upcomingDividends = $request->upcomingDividends ?? [];

        return PortfolioHelper::sortHoldings($upcomingDividends, $filters['sortBy'], $filters['sortDirection']);
    }

    public function sortDividendsReceived(Request $request)
    {
        $filters = [
            'sortBy'        => $request->sortBy ?? 'ticker',
            'sortDirection' => $request->sortDirection ?? 'desc',
        ];

        $dividendsReceived = $request->dividendsReceived ?? [];

        return PortfolioHelper::sortDividendsReceived($dividendsReceived, $filters['sortBy'], $filters['sortDirection']);
    }

    public function sortCashMovements(Request $request)
    {
        $filters = [
            'sortBy'        => $request->sortBy ?? 'ticker',
            'sortDirection' => $request->sortDirection ?? 'desc',
        ];

        $cashMovements = $request->cashMovements ?? [];

        return PortfolioHelper::sortCashMovements($cashMovements, $filters['sortBy'], $filters['sortDirection']);
    }

    public function importHoldings(Request $request)
    {
        $request->validate([
            'file'      => 'required|file|mimes:csv',
            'brokerage' => 'required',
        ]);

        $brokerage = $request->brokerage;
        $originalFilename = $request->file('file')->getClientOriginalName();
        $request->file('file')->storeAs("imports/$brokerage", $originalFilename, 'local'); // 'local' disk is private

        $portfolio = Portfolio::where('uid', $request->portfolioUid)->first();

        if ($request->brokerage === 'schwab') {
            $this->portfolioService->importHoldingsFromSchwab($portfolio->id, $request->file('file'));

            return back()->with('success', 'Holdings imported!');
        }

    }

    public function updateHoldings(UpdateHoldingRequest $request)
    {
        $portfolioUid = $request->portfolioUid;
        $holdings = $request->holdings;

        $portfolio = Portfolio::where('uid', $portfolioUid)->firstOrFail();

        $this->portfolioService->updateHoldings($portfolio->id, $holdings);

        return to_route('portfolios.show', ['portfolioUid' => $portfolio->uid]);
    }

    public function getPortfoliosToCompare(ComparePortfolioRequest $request)
    {
        $portfolio1 = Portfolio::where('uid', $request->portfolio1Uid)->firstOrFail();

        if ($request->portfolio2Uid) {
            $portfolio2 = Portfolio::where('uid', $request->portfolio2Uid)->firstOrFail();
        } else {
            $portfolio2 = Portfolio::query()
                ->where('id', '!=', $portfolio1->id)
                ->where('user_id', auth()->id())
                ->firstOrFail();
        }

        $portfoliosToCompare = $this->portfolioService->getPortfoliosForComparison($portfolio1->id, $portfolio2->id);

        return $portfoliosToCompare;
    }
}
