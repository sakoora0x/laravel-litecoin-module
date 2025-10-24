<?php

namespace sakoora0x\LaravelLitecoinModule\WebhookHandlers;

use sakoora0x\LaravelLitecoinModule\Models\LitecoinAddress;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinDeposit;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinWallet;

interface WebhookHandlerInterface
{
    public function handle(LitecoinWallet $wallet, LitecoinAddress $address, LitecoinDeposit $deposit): void;
}