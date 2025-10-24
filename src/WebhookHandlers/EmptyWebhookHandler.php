<?php

namespace sakoora0x\LaravelLitecoinModule\WebhookHandlers;

use Illuminate\Support\Facades\Log;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinAddress;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinDeposit;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinWallet;

class EmptyWebhookHandler implements WebhookHandlerInterface
{
    public function handle(LitecoinWallet $wallet, LitecoinAddress $address, LitecoinDeposit $deposit): void
    {
        Log::error('Litecoin Wallet '.$wallet->name.' new transaction '.$deposit->txid.' for address '.$address->address);
    }
}