<?php

namespace Mugennsou\LaravelWallet;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Mugennsou\LaravelWallet\Contracts\WalletInterface;
use Mugennsou\LaravelWallet\Facades\Wallet as WalletFacade;

/**
 * Trait HasWallets
 * @package Mugennsou\LaravelWallet
 * @property \Illuminate\Database\Eloquent\Collection|Wallet[] $wallets
 */
trait HasWallets
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function wallets(): MorphMany
    {
        return $this->morphMany(Wallet::class, 'user');
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasWallet(string $name): bool
    {
        return $this->wallets->contains('name', $name);
    }

    /**
     * @param string $name
     * @return WalletInterface
     */
    public function wallet(string $name): WalletInterface
    {
        return $this->wallets->firstWhere('name', $name);
    }

    /**
     * Determine if the user can withdraw the given amount.
     *
     * @param int $amount
     * @return bool
     * @throws \Throwable
     */
    public function canWithdraw(int $amount): bool
    {
        return WalletFacade::for($this)->canWithdraw($amount);
    }

    /**
     * Deposit wallet default currency.
     *
     * @param int $amount
     * @param array $reason
     * @return bool
     * @throws \Throwable
     */
    public function deposit(int $amount, array $reason = []): bool
    {
        return WalletFacade::for($this)->deposit($amount, $reason);
    }

    /**
     * Withdraw wallet default currency.
     *
     * @param int $amount
     * @param array $reason
     * @return bool
     * @throws \Throwable
     */
    public function withdraw(int $amount, $reason = []): bool
    {
        return WalletFacade::for($this)->withdraw($amount, $reason);
    }
}
