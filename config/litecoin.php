<?php

return [
    /*
     * Sets the handler to be used when Litecoin Wallet has a new deposit.
     */
    'webhook_handler' => \Mollsoft\LaravelLitecoinModule\WebhookHandlers\EmptyWebhookHandler::class,

    /*
     * Set address type of generate new addresses.
     */
    'address_type' => \Mollsoft\LaravelLitecoinModule\Enums\AddressType::BECH32,

    /*
     * Set model class for both LitecoinWallet, LitecoinAddress, LitecoinDeposit,
     * to allow more customization.
     *
     * LitecoindRpcApi model must be or extend `Mollsoft\LaravelLitecoinModule\LitecoindRpcApi::class`
     * LitecoinNode model must be or extend `Mollsoft\LaravelLitecoinModule\Models\LitecoinNode::class`
     * LitecoinWallet model must be or extend `Mollsoft\LaravelLitecoinModule\Models\LitecoinWallet::class`
     * LitecoinAddress model must be or extend `Mollsoft\LaravelLitecoinModule\Models\LitecoinAddress::class`
     * LitecoinDeposit model must be or extend `Mollsoft\LaravelLitecoinModule\Models\LitecoinDeposit::class`
     */
    'models' => [
        'rpc_client' => \Mollsoft\LaravelLitecoinModule\LitecoindRpcApi::class,
        'node' => \Mollsoft\LaravelLitecoinModule\Models\LitecoinNode::class,
        'wallet' => \Mollsoft\LaravelLitecoinModule\Models\LitecoinWallet::class,
        'address' => \Mollsoft\LaravelLitecoinModule\Models\LitecoinAddress::class,
        'deposit' => \Mollsoft\LaravelLitecoinModule\Models\LitecoinDeposit::class,
    ],
];
