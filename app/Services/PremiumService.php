<?php

namespace App\Services;

use App\Helpers\NumberHelper;
use App\Jobs\CheckBitcoinPaymentJob;
use App\Models\BtcAddress;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PremiumService
{
    public function getCurrentPlan()
    {
        $user = auth()->user();

        $expiresAt = $user->premium_expires_at ? Carbon::parse($user->premium_expires_at) : null;

        if ($user->is_premium) {
            if ($expiresAt && $expiresAt->year > 2100) {
                $plan = 'Lifetime';
                $expiresIn = 'Forever';
            } else {
                $plan = 'Premium';
                $expiresIn = $expiresAt?->format('F j, Y');
            }
        } else {
            $plan = 'Free';
            $expiresIn = null;
        }

        return [
            'plan'      => $plan,
            'expiresIn' => $expiresIn,
        ];
    }

    public function getPendingPayment()
    {
        $user = auth()->user();

        $pendingPayment = Payment::query()
            ->with('btcAddress')
            ->where('user_id', $user->id)
            ->where('confirmed_at', null)
            ->latest()
            ->first();

        if (!$pendingPayment) {
            return null;
        }

        // Uid
        $uid = $pendingPayment->uid;

        // Plan
        $plan = $pendingPayment->plan;

        // BTC Price
        $btcPrice = $pendingPayment->btc_price;
        $btcPriceFormatted = NumberHelper::formatNumberToDollar($btcPrice);

        // Amount USD
        $amountUsd = $pendingPayment->amount_usd;
        $amountUsdFormatted = NumberHelper::formatNumberToDollar($amountUsd);

        // Amount BTC
        $amountBtc = $pendingPayment->amount_btc;

        // BTC QR
        $btcQr = $pendingPayment->btc_qr;

        // Confirmed At
        $confirmedAt = $pendingPayment->confirmed_at;

        // Expires At
        $expiresAt = $pendingPayment->expires_at;

        // Time Left
        $timeLeft = Carbon::now()->diff($expiresAt)->format('%I:%S');

        // BTC Address
        $btcAddress = $pendingPayment->btcAddress->address;

        // QR Code URL
        $qrCodeUrl = $pendingPayment->qrCodeUrl;

        return [
            // Uid
            'uid'                => $uid,

            // Plan
            'plan'               => $plan,

            // BTC Price
            'btcPrice'           => $btcPrice,
            'btcPriceFormatted'  => $btcPriceFormatted,

            // Amount USD
            'amountUsd'          => $amountUsdFormatted,
            'amountUsdFormatted' => $amountUsdFormatted,

            // Amount BTC
            'amountBtc'          => $amountBtc,

            // BTC QR
            'btcQr'              => $btcQr,

            // Confirmed At
            'confirmedAt'        => $confirmedAt,

            // Expires At
            'expiresAt'          => $expiresAt,

            // Time Left
            'timeLeft'           => $timeLeft,

            // QR Code URL
            'qrCodeUrl'          => $qrCodeUrl,

            // BTC Address
            'btcAddress'         => [
                'address' => $btcAddress
            ]
        ];
    }

    public function getPayments()
    {
        $user = auth()->user();

        return Payment::query()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($payment) {

                $plan = $payment->plan;
                $amountUsd = $payment->amount_usd;
                $amountUsdFormatted = NumberHelper::formatNumberToDollar($amountUsd);
                $amountBtc = $payment->amount_btc;
                $confirmedAt = $payment->confirmed_at ? Carbon::parse($payment->confirmed_at)->format('F j, Y') : null;
                $btcAddress = $payment->btcAddress->address;

                return [
                    'plan'               => $plan,
                    'amountUsd'          => $amountUsdFormatted,
                    'amountUsdFormatted' => $amountUsdFormatted,
                    'amountBtc'          => $amountBtc,
                    'confirmedAt'        => $confirmedAt,
                    'btcAddress'         => [
                        'address' => $btcAddress
                    ]
                ];
            });
    }

    public function storePayment($data)
    {
        $user = auth()->user();

        $pendingPayment = Payment::query()
            ->where('user_id', $user->id)
            ->where('confirmed_at', null)
            ->latest()
            ->first();

        if (!$pendingPayment) {
            try {
                DB::beginTransaction();

                $usedBtcAddresses = Payment::all()->pluck('btc_address_id')->toArray();
                $availableBtcAddress = BtcAddress::query()
                    ->whereNotIn('id', $usedBtcAddresses)
                    ->first();

                $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids'           => 'bitcoin',
                    'vs_currencies' => 'usd',
                ]);

                if (!$response->successful()) {
                    abort(503, 'Unable to fetch BTC price. Please try again later.');
                }

                $btcPrice = $response->json()['bitcoin']['usd'];

                $plan = $data['plan'];

                $yearlyPlanPriceUsd = 49;
                $lifetimePlanPriceUsd = 149;

                $yearlyPlanPriceBtc = floor(($yearlyPlanPriceUsd / $btcPrice) * 1e8) / 1e8;
                $lifetimePlanPriceBtc = floor(($lifetimePlanPriceUsd / $btcPrice) * 1e8) / 1e8;

                $amountUsd = ($plan === 'yearly') ? $yearlyPlanPriceUsd : $lifetimePlanPriceUsd;
                $amountBtc = ($plan === 'yearly') ? $yearlyPlanPriceBtc : $lifetimePlanPriceBtc;

                $payment = Payment::create([
                    'uid'            => Str::uuid()->toString(),
                    'user_id'        => $user->id,
                    'btc_address_id' => $availableBtcAddress->id,
                    'plan'           => $data['plan'],
                    'btc_price'      => $btcPrice,
                    'amount_usd'     => $amountUsd,
                    'amount_btc'     => $amountBtc,
                    'btc_qr'         => null,
                    'expires_at'     => Carbon::now()->addMinutes(180),
                    'confirmed_at'   => null,
                ]);

                DB::commit();

                CheckBitcoinPaymentJob::dispatch($payment->id)->delay(now()->addSeconds(10));

                return back();

            } catch (\Throwable $e) {
                DB::rollBack();

                throw $e;
            }
        }
    }
}
