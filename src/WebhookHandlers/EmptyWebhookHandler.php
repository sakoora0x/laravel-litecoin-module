<?php

namespace Mollsoft\LaravelLitecoinModule\WebhookHandlers;

use Illuminate\Support\Facades\Log;
use Mollsoft\LaravelLitecoinModule\Models\LitecoinAddress;
use Mollsoft\LaravelLitecoinModule\Models\LitecoinDeposit;
use Mollsoft\LaravelLitecoinModule\Models\LitecoinWallet;

class EmptyWebhookHandler implements WebhookHandlerInterface
{
    public function handle(LitecoinWallet $wallet, LitecoinAddress $address, LitecoinDeposit $deposit): void
    {
        Log::error('Litecoin Wallet '.$wallet->name.' new transaction '.$deposit->txid.' for address '.$address->address);
    }
}