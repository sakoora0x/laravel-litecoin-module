<?php

namespace sakoora0x\LaravelLitecoinModule\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use sakoora0x\LaravelLitecoinModule\LitecoinServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->runMigrations();
    }

    protected function runMigrations(): void
    {
        $migrations = [
            'create_litecoin_nodes_table',
            'create_litecoin_wallets_table',
            'create_litecoin_addresses_table',
            'create_litecoin_deposits_table',
        ];

        foreach ($migrations as $migration) {
            $file = __DIR__ . '/../database/migrations/' . $migration . '.php.stub';
            if (file_exists($file)) {
                $migrationClass = include $file;
                $migrationClass->up();
            }
        }
    }

    protected function getPackageProviders($app): array
    {
        return [
            LitecoinServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Litecoin' => \sakoora0x\LaravelLitecoinModule\Facades\Litecoin::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup encryption key for testing
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Setup package configuration
        $app['config']->set('litecoin.webhook_handler', \sakoora0x\LaravelLitecoinModule\WebhookHandlers\EmptyWebhookHandler::class);
        $app['config']->set('litecoin.address_type', \sakoora0x\LaravelLitecoinModule\Enums\AddressType::BECH32);
        $app['config']->set('litecoin.models', [
            'rpc_client' => \sakoora0x\LaravelLitecoinModule\LitecoindRpcApi::class,
            'node' => \sakoora0x\LaravelLitecoinModule\Models\LitecoinNode::class,
            'wallet' => \sakoora0x\LaravelLitecoinModule\Models\LitecoinWallet::class,
            'address' => \sakoora0x\LaravelLitecoinModule\Models\LitecoinAddress::class,
            'deposit' => \sakoora0x\LaravelLitecoinModule\Models\LitecoinDeposit::class,
        ]);
    }
}
