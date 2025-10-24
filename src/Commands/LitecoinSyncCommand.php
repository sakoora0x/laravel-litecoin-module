<?php

namespace sakoora0x\LaravelLitecoinModule\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinWallet;
use sakoora0x\LaravelLitecoinModule\Services\SyncService;

class LitecoinSyncCommand extends Command
{
    protected $signature = 'litecoin:sync';

    protected $description = 'Litecoin sync wallets';

    public function handle(): void
    {
        /** @var class-string<LitecoinWallet> $model */
        $model = config('litecoin.models.wallet');

        $model::orderBy('id')
            ->each(function (LitecoinWallet $wallet) {
                $this->info("Litecoin Wallet $wallet->name starting sync...");

                try {
                    App::make(SyncService::class, [
                        'wallet' => $wallet
                    ])->run();

                    $this->info("Litecoin Wallet $wallet->name successfully sync finished!");
                }
                catch(\Exception $e) {
                    $this->error("Error: {$e->getMessage()}");
                }
            });
    }
}
