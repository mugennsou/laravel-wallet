<?php

namespace Mugennsou\LaravelWallet\Contracts;

interface WalletCurrencyInterface
{
    /**
     * Get currency amount.
     *
     * @return int
     */
    public function amount(): int;

    /**
     * Deposit currency.
     *
     * @param int $amount
     * @return bool
     */
    public function deposit(int $amount): bool;

    /**
     * Withdraw currency.
     *
     * @param int $amount
     * @return bool
     */
    public function withdraw(int $amount): bool;
}
