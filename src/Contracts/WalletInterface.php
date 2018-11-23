<?php

namespace Mugennsou\LaravelWallet\Contracts;

interface WalletInterface
{
    /**
     * Check wallet currency exists.
     *
     * @param string $name
     * @return bool
     */
    public function hasCurrency(string $name): bool;

    /**
     * Get wallet currency.
     *
     * @param string|null $name
     * @return WalletCurrencyInterface
     */
    public function useCurrency(string $name = null): WalletCurrencyInterface;

    /**
     * Check balance need password.
     *
     * @return bool
     */
    public function guardForAmount(): bool;

    /**
     * Overdraft the wallet.
     *
     * @return bool
     */
    public function overdraft(): bool;

    /**
     * Check wallet password is right.
     *
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool;

    /**
     * Update wallet password.
     *
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(string $newPassword): bool;
}
