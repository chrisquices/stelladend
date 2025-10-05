<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidTransactionException;
use App\Http\Requests\CashMovements\StoreCashMovementRequest;
use App\Http\Requests\CashMovements\UpdateCashMovementNoteRequest;
use App\Http\Requests\CashMovements\UpdateCashMovementRequest;
use App\Http\Requests\Transactions\StoreTransactionRequest;
use App\Http\Requests\Transactions\UpdateTransactionNoteRequest;
use App\Http\Requests\Transactions\UpdateTransactionRequest;
use App\Models\CashMovement;
use App\Models\Portfolio;
use App\Models\Security;
use App\Models\Transaction;
use App\Services\CashMovementService;
use Illuminate\Validation\ValidationException;

class CashMovementController extends Controller
{
    private $cashMovementService;

    public function __construct()
    {
        $this->cashMovementService = new CashMovementService();
    }

    public function store(StoreCashMovementRequest $request)
    {
        $portfolio = Portfolio::where('uid', $request->portfolioUid)->firstOrFail();
        $type = $request->type;
        $amount = $request->amount;
        $description = $request->description;
        $note = $request->note;
        $date = $request->date;

        $this->cashMovementService->store(portfolioId: $portfolio->id, type: $type, amount: $amount, description: $description, note: $note, date: $date);

        return back();
    }

    public function update(UpdateCashMovementRequest $request, $portfolioUid, $pp)
    {
        $cashMovement = CashMovement::where('uid', $request->cashMovementUid)->firstOrFail();
        $type = $request->type;
        $amount = $request->amount;
        $description = $request->description;
        $note = $request->note;
        $date = $request->date;

        $this->cashMovementService->update(cashMovementId: $cashMovement->id, type: $type, amount: $amount, description: $description, note: $note, date: $date);

        return back();
    }

    public function updateNote(UpdateCashMovementNoteRequest $request, $cashMovementUid)
    {
        $cashMovement = CashMovement::where('uid', $cashMovementUid)->firstOrFail();

        $this->cashMovementService->updateNote($cashMovement->id, $request->note);

        return back();
    }

    public function delete($cashMovementUid)
    {
        $cashMovement = CashMovement::where('uid', $cashMovementUid)->firstOrFail();

        try {
            $this->cashMovementService->delete($cashMovement->id);

            return back();

        } catch (InvalidTransactionException $e) {
            throw ValidationException::withMessages([
                'transaction' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'transaction' => 'An unexpected error occurred.',
            ]);
        }
    }
}
