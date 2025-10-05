<?php

namespace App\Jobs;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckBitcoinPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $paymentId;

    public function __construct(int $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function handle()
    {
        $payment = Payment::find($this->paymentId);

        if (!$payment || !$payment->expires_at) {
            return;
        }

        if (now()->greaterThan($payment->expires_at)) {
            $payment->delete();
            return;
        }

        $address = $payment->btcAddress->address;
        $url = "https://mempool.space/testnet4/api/address/{$address}";

        $response = Http::get($url);

        if ($response->failed()) {
            self::dispatch($this->paymentId)->delay(now()->addSeconds(10));
            return;
        }

        $data = $response->json();

        $expectedSats = (int)round($payment->amount_btc * 100_000_000); // Convert BTC to sat
        $btcPrice = $payment->btc_price; // BTC price at time of payment creation
        $marginUSD = 1.00;
        $marginSats = (int)round(($marginUSD / $btcPrice) * 100_000_000);
        $receivedSats = $data['chain_stats']['funded_txo_sum'] ?? 0;

        if ($receivedSats >= ($expectedSats - $marginSats)) {
            $amountBtc = $receivedSats / 100_000_000;
            $amountUsd = $amountBtc * $btcPrice;

            // Determine plan based on USD amount
            $plan = match (true) {
                $amountUsd > 4 && $amountUsd < 6   => 'Yearly',
                $amountUsd > 19 && $amountUsd < 21 => 'Lifetime',
                default                              => 'Unknown',
            };

            // Update payment record
            $payment->update([
                'confirmed_at' => now(),
                'amount_usd'   => $amountUsd,
                'amount_btc'   => $amountBtc,
                'expires_at'   => null,
            ]);

            // If valid plan, upgrade user
            if (in_array($plan, ['Yearly', 'Lifetime'])) {
                $user = $payment->user;
                $user->is_premium = true;

                $currentExpiry = $user->premium_expires_at ?? now();

                $user->premium_expires_at = match ($plan) {
                    'Yearly'   => Carbon::parse($currentExpiry)->addYear(),
                    'Lifetime' => Carbon::parse($currentExpiry)->addYears(99),
                };

                $user->save();
            }
        } else {
            self::dispatch($this->paymentId)->delay(now()->addSeconds(10));
        }
    }
}
