<?php

namespace App\Http\Requests\Transactions;

use App\Models\Portfolio;
use App\Models\Security;
use App\Models\TransactionMetric;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'portfolioUid'       => ['required', 'exists:portfolios,uid'],
            'securityUid'        => ['required', 'exists:securities,uid'],
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
        $portfolioUid = $this->input('portfolioUid');
        $securityUid = $this->input('securityUid');

        $portfolio = Portfolio::where('uid', $portfolioUid)->first();
        $security = Security::where('uid', $securityUid)->first();

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
