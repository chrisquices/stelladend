<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payments\StorePaymentRequest;
use App\Models\User;
use App\Services\PortfolioService;
use App\Services\PreferenceService;
use App\Services\PremiumService;
use App\Services\UserService;
use App\Services\WatchlistService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class AuthGoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect('/')->with('error', 'Google authentication failed.');
        }

        // Check if the user already exists in the database
        $existingUser = User::where('email', $user->email)->first();

        if ($existingUser) {
            // Log the user in if they already exist
            Auth::login($existingUser);
        } else {
            // Otherwise, create a new user and log them in
            $newUser = User::updateOrCreate([
                'email' => $user->email
            ], [
                'password'          => bcrypt(Str::random(16)),
                'email_verified_at' => now()
            ]);

            Auth::login($newUser);

            app(UserService::class)->upgradeToPremium();

            app(PreferenceService::class)->storeStarterPreferences();

            app(PortfolioService::class)->storeStarterPortfolio();

            app(WatchlistService::class)->storeStarterWatchlist();
        }

        // Redirect the user to the dashboard or any other secure page
        return to_route('dashboard.index');
    }
}
