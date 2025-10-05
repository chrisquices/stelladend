<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\WatchlistSecurityController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CashMovementController;
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AuthGoogleController;

// Public
//Route::get('/auth/google/redirect', [AuthGoogleController::class, 'redirect'])->name('auth.google.redirect');
//Route::get('/auth/google/callback', [AuthGoogleController::class, 'callback'])->name('auth.google.callback');

Route::get('/', [IndexController::class, 'index'])->name('index');

Route::get('/about-us', [IndexController::class, 'aboutUs'])->name('about-us');
Route::get('/terms-and-conditions', [IndexController::class, 'termsAndConditions'])->name('terms-and-conditions');
Route::get('/privacy-policy', [IndexController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/refund-policy', [IndexController::class, 'refundPolicy'])->name('refund-policy');
Route::get('/contact-us', [IndexController::class, 'contactUs'])->name('contact-us');

// Portfolios
Route::prefix('p')->name('public.')->group(function () {
    Route::get('/{publicReferenceCode}', [PortfolioController::class, 'showPublic'])->name('show');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Feedback
    Route::prefix('feedback')->name('feedback.')->group(function () {
        Route::post('/store', [FeedbackController::class, 'store'])->name('store');
    });

    // Dashboard
    Route::prefix('')->name('dashboard.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('index');
    });

    // Securities
    Route::prefix('securities')->name('securities.')->group(function () {
        Route::get('/', [SecurityController::class, 'index'])->name('index');
    });

    // Portfolios
    Route::prefix('portfolios')->name('portfolios.')->group(function () {

        Route::get('/get-securities', [PortfolioController::class, 'getSecurities'])->name('get-securities');
        Route::get('/get-portfolios-to-compare', [PortfolioController::class, 'getPortfoliosToCompare'])->name('get-portfolios-to-compare');
        Route::post('/update-holdings', [PortfolioController::class, 'updateHoldings'])->name('update-holdings');
        Route::post('/import-holdings', [PortfolioController::class, 'importHoldings'])->name('import-holdings');
        Route::post('/sort-holdings', [PortfolioController::class, 'sortHoldings'])->name('sort-holdings');
        Route::post('/sort-upcoming-dividends', [PortfolioController::class, 'sortUpcomingDividends'])->name('sort-upcoming-dividends');
        Route::post('/sort-dividends-received', [PortfolioController::class, 'sortDividendsReceived'])->name('sort-dividends-received');
        Route::post('/sort-cash-movements', [PortfolioController::class, 'sortCashMovements'])->name('sort-cash-movements');

        Route::get('/', [PortfolioController::class, 'index'])->name('index');
        Route::post('/store', [PortfolioController::class, 'store'])->name('store');
        Route::get('/{portfolioUid}', [PortfolioController::class, 'show'])->name('show');
        Route::patch('/{portfolioUid}/update', [PortfolioController::class, 'update'])->name('update');
        Route::patch('/{portfolioUid}/switch-mode', [PortfolioController::class, 'switchMode'])->name('switch-mode');
        Route::patch('/{portfolioUid}/wipe', [PortfolioController::class, 'wipe'])->name('wipe');
        Route::delete('/{portfolioUid}/delete', [PortfolioController::class, 'delete'])->name('delete');

        // Cash Movements
        Route::prefix('cash-movements')->name('cash-movements.')->group(function () {
            Route::post('/store', [CashMovementController::class, 'store'])->name('store');
            Route::patch('/{cashMovementUid}/update', [CashMovementController::class, 'update'])->name('update');
            Route::patch('/{cashMovementUid}/update-note', [CashMovementController::class, 'updateNote'])->name('update-note');
            Route::delete('/{cashMovementUid}/delete', [CashMovementController::class, 'delete'])->name('delete');
        });

        // Transactions
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::post('/store', [TransactionController::class, 'store'])->name('store');
            Route::patch('/{transactionUid}/update', [TransactionController::class, 'update'])->name('update');
            Route::patch('/{transactionUid}/update-note', [TransactionController::class, 'updateNote'])->name('update-note');
            Route::delete('/{transactionUid}/delete', [TransactionController::class, 'delete'])->name('delete');
        });
    });

    // Accounts
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::post('/', [AccountController::class, 'store'])->name('store');
        Route::patch('/{accountUid}/update', [AccountController::class, 'update'])->name('update');
        Route::delete('/{accountUid}/delete', [AccountController::class, 'delete'])->name('delete');
    });

    // Watchlists
    Route::prefix('watchlists')->name('watchlists.')->group(function () {
        Route::post('/export', [WatchlistController::class, 'export'])->name('export');
        Route::get('/get-securities', [WatchlistController::class, 'getSecurities'])->name('get-securities');
        Route::post('/sort-watchlist-securities', [WatchlistController::class, 'sortWatchlistSecurities'])->name('sort-watchlist-securities');

        Route::get('/', [WatchlistController::class, 'index'])->name('index');
        Route::post('/store', [WatchlistController::class, 'store'])->name('store');
        Route::get('/{watchlistUid}', [WatchlistController::class, 'show'])->name('show');
        Route::patch('/{watchlistUid}/update', [WatchlistController::class, 'update'])->name('update');
        Route::delete('/{watchlistUid}/delete', [WatchlistController::class, 'delete'])->name('delete');

        Route::prefix('/{watchlistUid}/watchlist-securities')->name('watchlist-securities.')->group(function () {
            Route::post('/store', [WatchlistSecurityController::class, 'store'])->name('store');
            Route::delete('/{securityUid}/delete', [WatchlistSecurityController::class, 'delete'])->name('delete');
        });
    });

    // Calendar
    Route::prefix('calendar')->name('calendar.')->group(function () {
        Route::get('/', [CalendarController::class, 'index'])->name('index');

        Route::patch('/toggle-portfolio-inclusion', [CalendarController::class, 'togglePortfolioInclusion'])->name('toggle-portfolio-inclusion');
        Route::patch('/toggle-watchlist-inclusion', [CalendarController::class, 'toggleWatchlistInclusion'])->name('toggle-watchlist-inclusion');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');

        Route::patch('/update-account-settings', [SettingController::class, 'updateAccountSettings'])->name('update-account-settings');
        Route::patch('/update-password', [SettingController::class, 'updatePassword'])->name('update-password');
    });

    // Premium
    Route::prefix('premium')->name('premium.')->group(function () {
        Route::get('/', [PremiumController::class, 'index'])->name('index');
        Route::post('/store-payment', [PremiumController::class, 'storePayment'])->name('store-payment');
    });

    // Preferences
    Route::prefix('preferences')->name('preferences.')->group(function () {
        Route::patch('/', [PreferenceController::class, 'update'])->name('update');
    });

    Route::get('/og-image', [DashboardController::class, 'ogImage'])->name('og-image');

    Route::middleware(['admin'])->group(function () {

        // Admin
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');

            Route::post('/upload-stock-analysis-dividend-data', [AdminController::class, 'uploadStockAnalysisDividendData'])->name('upload-stock-analysis-dividend-data');
        });

    });
});

// Tools
Route::middleware(['admin'])->group(function () {

    // Logs
    Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
});


require __DIR__.'/auth.php';
