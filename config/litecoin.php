<?php

return [
    /*
     * Sets the handler to be used when Litecoin Wallet has a new deposit.
     */
    'webhook_handler' => \sakoora0x\LaravelLitecoinModule\WebhookHandlers\EmptyWebhookHandler::class,

    /*
     * Set address type of generate new addresses.
     */
    'address_type' => \sakoora0x\LaravelLitecoinModule\Enums\AddressType::BECH32,

    /*
     * Set model class for both LitecoinWallet, LitecoinAddress, LitecoinDeposit,
     * to allow more customization.
     *
     * LitecoindRpcApi model must be or extend `sakoora0x\LaravelLitecoinModule\LitecoindRpcApi::class`
     * LitecoinNode model must be or extend `sakoora0x\LaravelLitecoinModule\Models\LitecoinNode::class`
     * LitecoinWallet model must be or extend `sakoora0x\LaravelLitecoinModule\Models\LitecoinWallet::class`
     * LitecoinAddress model must be or extend `sakoora0x\LaravelLitecoinModule\Models\LitecoinAddress::class`
     * LitecoinDeposit model must be or extend `sakoora0x\LaravelLitecoinModule\Models\LitecoinDeposit::class`
     */
    'models' => [
        'rpc_client' => \sakoora0x\LaravelLitecoinModule\LitecoindRpcApi::class,
        'node' => \sakoora0x\LaravelLitecoinModule\Models\LitecoinNode::class,
        'wallet' => \sakoora0x\LaravelLitecoinModule\Models\LitecoinWallet::class,
        'address' => \sakoora0x\LaravelLitecoinModule\Models\LitecoinAddress::class,
        'deposit' => \sakoora0x\LaravelLitecoinModule\Models\LitecoinDeposit::class,
    ],
];
