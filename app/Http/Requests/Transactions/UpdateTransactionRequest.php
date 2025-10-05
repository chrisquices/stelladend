<?php

namespace App\Http\Requests\Transactions;

use App\Models\Portfolio;
use App\Models\Security;
use App\Models\Transaction;
use App\Models\TransactionMetric;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type'               => ['required', 'in:buy,sell'],
            'shares'             => ['required', 'numeric'],
            'price'              => ['required', 'numeric'],
            'feesAndCommissions' => ['nullable', 'numeric'],
            'tradeDate'          => ['required', 'date'],
            'note'               => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('type') === 'sell' && !$this->hasSufficientHoldings()) {
                $validator->errors()->add('volume', "Insufficient shares");
            }
        });
    }

    protected function hasSufficientHoldings(): bool
    {
        $transaction = Transaction::findOrFail($this->input('transactionId'));
        $portfolio = Portfolio::findOrFail($transaction->portfolio_id);
        $security = Security::findOrFail($transaction->security_id);

        $transactionMetrics = TransactionMetric::query()
            ->where('portfolio_id', $portfolio->id)
            ->where('security_id', $security->id)
            ->first();

        if (!$transactionMetrics) {
            return false;
        }

        $currentSharesOwned = $transactionMetrics->current_shares_owned;

        // You're selling more than what you own
        if ($this->input('shares') > $currentSharesOwned) {
            return false;
        }

        return true;
    }
}
