<?php

use sakoora0x\LaravelLitecoinModule\Commands\LitecoinSyncCommand;
use sakoora0x\LaravelLitecoinModule\Commands\LitecoinSyncWalletCommand;
use sakoora0x\LaravelLitecoinModule\Commands\LitecoinWebhookCommand;
use sakoora0x\LaravelLitecoinModule\Litecoin;

describe('LitecoinServiceProvider', function () {
    test('it registers the Litecoin singleton', function () {
        $litecoin = app(Litecoin::class);

        expect($litecoin)->toBeInstanceOf(Litecoin::class);
        expect(app(Litecoin::class))->toBe($litecoin);
    });

    test('it registers the Litecoin facade', function () {
        expect(class_exists('Litecoin'))->toBeTrue();
    });

    test('it publishes config file', function () {
        expect(config('litecoin'))->toBeArray()
            ->and(config('litecoin.webhook_handler'))->not->toBeNull()
            ->and(config('litecoin.address_type'))->not->toBeNull()
            ->and(config('litecoin.models'))->toBeArray();
    });

    test('it loads migrations', function () {
        $migrationFiles = [
            'create_litecoin_nodes_table',
            'create_litecoin_wallets_table',
            'create_litecoin_addresses_table',
            'create_litecoin_deposits_table',
        ];

        foreach ($migrationFiles as $migration) {
            expect(\Schema::hasTable(str_replace('create_', '', str_replace('_table', '', $migration))))->toBeTrue();
        }
    });

    test('it registers commands', function () {
        expect($this->app->make(LitecoinSyncCommand::class))->toBeInstanceOf(LitecoinSyncCommand::class)
            ->and($this->app->make(LitecoinSyncWalletCommand::class))->toBeInstanceOf(LitecoinSyncWalletCommand::class)
            ->and($this->app->make(LitecoinWebhookCommand::class))->toBeInstanceOf(LitecoinWebhookCommand::class);
    });

    test('it can resolve configured models', function () {
        $rpcClient = config('litecoin.models.rpc_client');
        $node = config('litecoin.models.node');
        $wallet = config('litecoin.models.wallet');
        $address = config('litecoin.models.address');
        $deposit = config('litecoin.models.deposit');

        expect($rpcClient)->toBe(\sakoora0x\LaravelLitecoinModule\LitecoindRpcApi::class)
            ->and($node)->toBe(\sakoora0x\LaravelLitecoinModule\Models\LitecoinNode::class)
            ->and($wallet)->toBe(\sakoora0x\LaravelLitecoinModule\Models\LitecoinWallet::class)
            ->and($address)->toBe(\sakoora0x\LaravelLitecoinModule\Models\LitecoinAddress::class)
            ->and($deposit)->toBe(\sakoora0x\LaravelLitecoinModule\Models\LitecoinDeposit::class);
    });

    test('it can resolve webhook handler', function () {
        $handler = config('litecoin.webhook_handler');

        expect($handler)->toBe(\sakoora0x\LaravelLitecoinModule\WebhookHandlers\EmptyWebhookHandler::class);

        $instance = app($handler);

        expect($instance)->toBeInstanceOf(\sakoora0x\LaravelLitecoinModule\WebhookHandlers\WebhookHandlerInterface::class);
    });

    test('it can resolve address type configuration', function () {
        $addressType = config('litecoin.address_type');

        expect($addressType)->toBeInstanceOf(\sakoora0x\LaravelLitecoinModule\Enums\AddressType::class)
            ->and($addressType)->toBe(\sakoora0x\LaravelLitecoinModule\Enums\AddressType::BECH32);
    });
});
