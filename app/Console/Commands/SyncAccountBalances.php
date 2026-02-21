<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attribute;
use App\Models\Transaction;

class SyncAccountBalances extends Command
{
    protected $signature = 'accounts:sync-balances {--dry-run}';
    protected $description = 'Recalculate account balances from transactions';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $accounts = Attribute::where('type', 10)
            ->where('status', '<>', 'temp')
            ->get(['id','name','amount','usd_amount']);

        $bar = $this->output->createProgressBar($accounts->count());

        foreach ($accounts as $account) {
            $bdt = Transaction::accountBalance($account->id, null, null, 'BDT');
            $usd = Transaction::accountBalance($account->id, null, null, 'USD');

            if (!$dryRun) {
                $account->amount = $bdt;
                $account->usd_amount = $usd;
                $account->save();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info($dryRun ? 'Dry run complete.' : 'Account balances synced.');
    }
}
