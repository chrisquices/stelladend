<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payments\StorePaymentRequest;
use App\Services\PremiumService;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function index()
    {
        return to_route('dashboard.index');

        if (auth()->check()) {
            return to_route('dashboard.index');
        }

        return Inertia::render('index/Index');
    }

    public function aboutUs() {
        return Inertia::render('index/AboutUs');
    }

    public function termsAndConditions() {
        return Inertia::render('index/TermsAndConditions');
    }

    public function privacyPolicy() {
        return Inertia::render('index/PrivacyPolicy');
    }

    public function refundPolicy() {
        return Inertia::render('index/RefundPolicy');
    }

    public function contactUs() {
        return Inertia::render('index/ContactUs');
    }
}
