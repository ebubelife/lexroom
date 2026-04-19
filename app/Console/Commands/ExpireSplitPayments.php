<?php

namespace App\Console\Commands;

use App\Jobs\ExpireUnpaidSplitPayments;
use Illuminate\Console\Command;

class ExpireSplitPayments extends Command
{
    protected $signature   = 'payments:expire-splits';
    protected $description = 'Expire unpaid split payments and move funds to escrow';

    public function handle()
    {
        ExpireUnpaidSplitPayments::dispatch();
        $this->info('Expiry job dispatched.');
        return 0;
    }
}
