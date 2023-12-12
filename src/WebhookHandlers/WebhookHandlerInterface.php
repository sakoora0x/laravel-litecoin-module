<?php

namespace Mollsoft\LaravelLitecoinModule\WebhookHandlers;

use Mollsoft\LaravelLitecoinModule\Models\LitecoinAddress;
use Mollsoft\LaravelLitecoinModule\Models\LitecoinDeposit;
use Mollsoft\LaravelLitecoinModule\Models\LitecoinWallet;

interface WebhookHandlerInterface
{
    public function handle(LitecoinWallet $wallet, LitecoinAddress $address, LitecoinDeposit $deposit): void;
}