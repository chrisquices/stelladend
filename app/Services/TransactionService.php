<?php

namespace App\Services;

use App\Exceptions\InvalidTransactionException;
use App\Models\CashMovement;
use App\Models\Transaction;
use App\Models\TransactionMetric;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    public function store($portfolioId, $securityId, $type, $shares, $price, $feesAndCommissions, $tradeDate, $note)
    {
        // Trade Value
        $tradeValue = $shares * $price;

        // Total
        $total = ($shares * $price) + $feesAndCommissions;

        if (in_array($type, ['buy', 'buy_drip'])) {
            $total *= -1;
        }

        try {
            DB::beginTransaction();

            $transaction = Transaction::create([
                'uid'                  => Str::uuid()->toString(),
                'portfolio_id'         => $portfolioId,
                'security_id'          => $securityId,
                'type'                 => $type, // buy or sell
                'shares'               => $shares,
                'price'                => $price,
                'fees_and_commissions' => $feesAndCommissions ?? 0,
                'total'                => $total,
                'trade_value'          => $tradeValue,
                'note'                 => $note,
                'trade_date'           => $tradeDate,
                'is_imported'          => false
            ]);

            // Create cash_movement records
            $security = $transaction->security;

            if ($type === 'buy' || $type === 'buy_drip') {
                CashMovement::create([ // TODO replace all Cash Movement creations with a store() from cashMovementService.php
                    'uid'            => Str::uuid()->toString(),
                    'portfolio_id'   => $portfolioId,
                    'transaction_id' => $transaction->id,
                    'type'           => 'security_buy',
                    'amount'         => $transaction->total,
                    'description'    => "$security->ticker $security->name",
                    'date'           => $transaction->trade_date,
                    'is_imported'    => false,
                ]);
            }

            if ($type === 'sell') {
                CashMovement::create([
                    'uid'            => Str::uuid()->toString(),
                    'portfolio_id'   => $portfolioId,
                    'transaction_id' => $transaction->id,
                    'type'           => 'security_sell',
                    'amount'         => $transaction->total,
                    'description'    => "$security->ticker $security->name",
                    'date'           => $transaction->trade_date,
                    'is_imported'    => false,
                ]);
            }

            app(PortfolioService::class)->removeIsImportOnlyFlag($portfolioId);

            app(PortfolioService::class)->recalculate($portfolioId);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $transaction;
    }

    public function update($transactionId, $type, $shares, $price, $feesAndCommissions, $tradeDate, $note)
    {
        $transaction = Transaction::findOrFail($transactionId);
        $portfolio = $transaction->portfolio;
        $security = $transaction->security;

        // Trade Value
        $tradeValue = $shares * $price;

        // Total
        $total = ($shares * $price) + $feesAndCommissions;

        if (in_array($type, ['buy', 'buy_drip'])) {
            $total *= -1;
        }

        try {
            DB::beginTransaction();

            $transaction->update([
                'type'                 => $type, // buy, buy_drip or sell
                'shares'               => $shares,
                'price'                => $price,
                'fees_and_commissions' => $feesAndCommissions ?? 0,
                'total'                => $total,
                'trade_value'          => $tradeValue,
                'note'                 => $note,
                'trade_date'           => $tradeDate,
            ]);

            // Update cash_movement records
            $relatedCashMovement = CashMovement::where('transaction_id', $transaction->id)->first();

            if ($relatedCashMovement) {

                $cashMovementType = 'unknown';

                if ($type === 'buy' || $type === 'buy_drip') $cashMovementType = 'security_buy';
                if ($type === 'sell' || $type === 'sell_drip') $cashMovementType = 'security_sell';

                $relatedCashMovement->update([
                    'type'   => $cashMovementType,
                    'amount' => $transaction->total,
                    'date'   => $transaction->trade_date,
                ]);
            }

            app(PortfolioService::class)->removeIsImportOnlyFlag($portfolio->id);

            app(PortfolioService::class)->recalculate($transaction->portfolio_id);

            $transactionMetric = TransactionMetric::query()
                ->where('portfolio_id', $portfolio->id)
                ->where('security_id', $security->id)
                ->first();

            if ($transactionMetric && $transactionMetric->current_shares_owned < 0) {
                throw new InvalidTransactionException('Error: Results in negative shares');
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $transaction;
    }

    public function updateNote($transactionId, $note)
    {
        $transaction = Transaction::findOrFail($transactionId);

        $transaction->update([
            'note' => $note,
        ]);

        return $transaction;
    }

    public function delete($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        $portfolio = $transaction->portfolio;
        $security = $transaction->security;

        try {
            DB::beginTransaction();

            $cashMovement = CashMovement::where('transaction_id', $transaction->id)->first();
            $cashMovement->delete();

            $transaction->delete();

            app(PortfolioService::class)->recalculate($transaction->portfolio_id);

            $transactionMetric = TransactionMetric::query()
                ->where('portfolio_id', $portfolio->id)
                ->where('security_id', $security->id)
                ->first();

            if ($transactionMetric && $transactionMetric->current_shares_owned < 0) {
                throw new InvalidTransactionException('Error: Results in negative shares');
            }

            app(PortfolioService::class)->removeIsImportOnlyFlag($portfolio->id);

            app(TransactionMetricService::class)->deleteTransactionMetricsWithZeroTransactions();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
