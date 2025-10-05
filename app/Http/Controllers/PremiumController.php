<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payments\StorePaymentRequest;
use App\Services\PremiumService;
use Inertia\Inertia;

class PremiumController extends Controller
{
    private $premiumService;

    public function __construct()
    {
        $this->premiumService = new PremiumService();
    }

    public function index()
    {
        $currentPlan = $this->premiumService->getCurrentPlan();
        $pendingPayment = $this->premiumService->getPendingPayment();
        $payments = $this->premiumService->getPayments();

        return Inertia::render('premium/Index', [
            'currentPlan'    => $currentPlan,
            'pendingPayment' => $pendingPayment,
            'payments'       => $payments,
        ]);
    }

    public function storePayment(StorePaymentRequest $request)
    {
        $this->premiumService->storePayment($request->validated());

        return back();
    }
}
