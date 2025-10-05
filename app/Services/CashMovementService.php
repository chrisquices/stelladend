<?php

namespace App\Services;

use App\Exceptions\InvalidTransactionException;
use App\Models\CashMovement;
use App\Models\Transaction;
use App\Models\TransactionMetric;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashMovementService
{
    public function store($portfolioId = null, $transactionId = null, $dividendReceivedId = null, $type = null, $amount = null, $description = null, $note = null, $date = null)
    {
        try {
            DB::beginTransaction();

            $cashMovement = CashMovement::create([
                'portfolio_id'         => $portfolioId,
                'transaction_id'       => $transactionId,
                'dividend_received_id' => $dividendReceivedId,
                'type'                 => $type,
                'amount'               => $amount,
                'description'          => $description,
                'note'                 => $note,
                'date'                 => $date,
                'is_imported'          => false,
            ]);

            app(PortfolioService::class)->removeIsImportOnlyFlag($portfolioId);

            app(PortfolioService::class)->recalculate($portfolioId);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $cashMovement;
    }

    public function update($cashMovementId, $type = null, $amount = null, $description = null, $note = null, $date = null)
    {
        $cashMovement = CashMovement::findOrFail($cashMovementId);
        $portfolio = $cashMovement->portfolio;

        try {
            DB::beginTransaction();

            $cashMovement->update([
                'type'        => $type ?? $cashMovement->type,
                'amount'      => $amount ?? $cashMovement->amount,
                'description' => $description ?? $cashMovement->description,
                'note'        => $note ?? $cashMovement->note,
                'date'        => $date ?? $cashMovement->date,
            ]);

            app(PortfolioService::class)->removeIsImportOnlyFlag($portfolio->id);

            app(PortfolioService::class)->recalculate($cashMovement->portfolio_id);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $cashMovement;
    }

    public function updateNote($cashMovementId, $note)
    {
        $cashMovement = CashMovement::findOrFail($cashMovementId);

        $cashMovement->update([
            'note' => $note,
        ]);

        return $cashMovement;
    }

    public function delete($cashMovementId)
    {
        $cashMovement = CashMovement::findOrFail($cashMovementId);
        $portfolio = $cashMovement->portfolio;

        try {
            DB::beginTransaction();

            $cashMovement->delete();

            app(PortfolioService::class)->removeIsImportOnlyFlag($portfolio->id);

            app(PortfolioService::class)->recalculate($cashMovement->portfolio_id);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
