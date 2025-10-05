<?php

namespace App\Http\Controllers;

use App\Http\Requests\Accounts\StoreAccountRequest;
use App\Http\Requests\Accounts\UpdateAccountRequest;
use App\Models\Account;
use App\Services\AccountService;
use Inertia\Inertia;

class AccountController extends Controller
{
    private $accountService;

    public function __construct()
    {
        $this->accountService = new AccountService();
    }

    public function index()
    {
        $accounts = $this->accountService->getAccounts();
        $currencies = $this->accountService->getCurrencies();

        return Inertia::render('accounts/Index', [
            'accounts'        => $accounts,
            'currencies'      => $currencies,
        ]);
    }

    public function store(StoreAccountRequest $request)
    {
        $account = $this->accountService->store();

        return to_route('accounts.index');
    }

    public function update(UpdateAccountRequest $request)
    {
        $account = Account::where('uid', $request->uid)->firstOrFail();

        $account = $this->accountService->update($account->id, $request->validated());

        return to_route('accounts.index');
    }

    public function delete($accountUid)
    {
        $account = Account::where('uid', $accountUid)->firstOrFail();

        $this->accountService->delete($account->id);

        return to_route('accounts.index');
    }
}
