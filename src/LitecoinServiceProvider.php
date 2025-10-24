<?php

namespace sakoora0x\LaravelLitecoinModule;

use sakoora0x\LaravelLitecoinModule\Commands\LitecoinSyncCommand;
use sakoora0x\LaravelLitecoinModule\Commands\LitecoinSyncWalletCommand;
use sakoora0x\LaravelLitecoinModule\Commands\LitecoinWebhookCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LitecoinServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('litecoin')
            ->hasConfigFile()
            ->hasMigrations([
                'create_litecoin_nodes_table',
                'create_litecoin_wallets_table',
                'create_litecoin_addresses_table',
                'create_litecoin_deposits_table',
            ])
            ->hasCommands([
                LitecoinSyncWalletCommand::class,
                LitecoinSyncCommand::class,
                LitecoinWebhookCommand::class,
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations();
            });
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Litecoin::class);
    }
}