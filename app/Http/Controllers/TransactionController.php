<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidTransactionException;
use App\Http\Requests\Transactions\StoreTransactionRequest;
use App\Http\Requests\Transactions\UpdateTransactionNoteRequest;
use App\Http\Requests\Transactions\UpdateTransactionRequest;
use App\Models\Portfolio;
use App\Models\Security;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    private $transactionService;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
    }

    public function store(StoreTransactionRequest $request)
    {
        $portfolioUid = $request->portfolioUid;
        $securityUid = $request->securityUid;
        $type = $request->type;
        $shares = $request->shares;
        $price = $request->price;
        $feesAndCommissions = $request->feesAndCommissions;
        $tradeDate = $request->tradeDate;
        $note = $request->note;

        $portfolio = Portfolio::where('uid', $portfolioUid)->firstOrFail();
        $security = Security::where('uid', $securityUid)->firstOrFail();

        $this->transactionService->store($portfolio->id, $security->id, $type, $shares, $price, $feesAndCommissions, $tradeDate, $note);

        return back();
    }

    public function update(UpdateTransactionRequest $request, $transactionUid)
    {
        $transaction = Transaction::where('uid', $transactionUid)->firstOrFail();

        $type = $request->type;
        $shares = $request->shares;
        $price = $request->price;
        $feesAndCommissions = $request->feesAndCommissions;
        $tradeDate = $request->tradeDate;
        $note = $request->note;

        try {
            $this->transactionService->update($transaction->id, $type, $shares, $price, $feesAndCommissions, $tradeDate, $note);

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

    public function updateNote(UpdateTransactionNoteRequest $request, $transactionUid)
    {
        $transaction = Transaction::where('uid', $transactionUid)->firstOrFail();

        $this->transactionService->updateNote($transaction->id, $request->note);

        return back();
    }

    public function delete($transactionUid)
    {
        $transaction = Transaction::where('uid', $transactionUid)->firstOrFail();

        try {
            $this->transactionService->delete($transaction->id);

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
