<?php

namespace Mugennsou\LaravelWallet;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class WalletServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_wallets_table.php'             =>
                    database_path('migrations/' . date('Y_m_d_His_') . 'create_wallets_table.php'),
                __DIR__ . '/../database/migrations/create_wallet_balances_table.php'     =>
                    database_path('migrations/' . date('Y_m_d_His_') . 'create_wallet_balances_table.php'),
                __DIR__ . '/../database/migrations/create_wallet_transactions_table.php' =>
                    database_path('migrations/' . date('Y_m_d_His_') . 'create_wallet_transactions_table.php'),
            ], 'wallet-migrations');

            $this->publishes([
                __DIR__ . '/../config/wallet.php' => config_path('wallet.php'),
            ], 'wallet-config');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/wallet.php', 'wallet');

        $this->registerWalletManager();
    }

    public function registerWalletManager()
    {
        $this->app->singleton('wallet', function (Application $app) {
            return new WalletManager($app);
        });

        $this->app->alias('wallet', WalletManager::class);
    }
}
