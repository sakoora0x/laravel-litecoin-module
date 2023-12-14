<?php

namespace Mollsoft\LaravelLitecoinModule;

use Mollsoft\LaravelLitecoinModule\Commands\LitecoinSyncCommand;
use Mollsoft\LaravelLitecoinModule\Commands\LitecoinSyncWalletCommand;
use Mollsoft\LaravelLitecoinModule\Commands\LitecoinWebhookCommand;
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
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations();
            });;

        $this->app->singleton(Litecoin::class);
    }
}