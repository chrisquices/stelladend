<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;

class DeleteExpiredUnconfirmedPayments extends Command
{
    protected $signature = 'app:delete-expired-unconfirmed-payments';
    protected $description = 'Delete unconfirmed payments that have expired';

    public function handle()
    {
        $deleted = Payment::query()
            ->whereNull('confirmed_at')
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Deleted {$deleted} expired unconfirmed payments.");
    }
}
