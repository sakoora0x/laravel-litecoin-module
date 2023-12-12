<?php

namespace Mollsoft\LaravelLitecoinModule;

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
            ->runsMigrations()
            ->hasCommands()
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations();
            });;

        $this->app->singleton(Litecoin::class);
    }
}