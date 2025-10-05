<?php

namespace App\Http\Controllers;

use App\Http\Requests\Feedback\StoreFeedbackRequest;
use App\Services\FeedbackService;

class FeedbackController extends Controller
{
    private $feedbackService;

    public function __construct()
    {
        $this->feedbackService = new FeedbackService();
    }

    public function store(StoreFeedbackRequest $request)
    {
        $watchlist = $this->feedbackService->store($request->validated());

        return to_route('watchlists.index', ['watchlistUid' => $watchlist->uid]);
    }
}
