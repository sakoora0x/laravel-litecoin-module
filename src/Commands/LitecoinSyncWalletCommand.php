<?php

namespace sakoora0x\LaravelLitecoinModule\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinWallet;
use sakoora0x\LaravelLitecoinModule\Services\SyncService;

class LitecoinSyncWalletCommand extends Command
{
    protected $signature = 'litecoin:sync-wallet {wallet_id}';

    protected $description = 'Sync Litecoin Wallet';

    public function handle(): void
    {
        $walletId = $this->argument('wallet_id');

        /** @var class-string<LitecoinWallet> $model */
        $model = config('litecoin.models.wallet');
        $wallet = $model::findOrFail($walletId);

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
    }
}
