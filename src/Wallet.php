<?php

namespace Mugennsou\LaravelWallet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Hash;
use Mugennsou\LaravelWallet\Contracts\WalletCurrencyInterface;
use Mugennsou\LaravelWallet\Contracts\WalletInterface;

/**
 * Class Wallet
 * @package Mugennsou\LaravelWallet
 * @property \Illuminate\Database\Eloquent\Collection|WalletBalance[] $balances
 */
class Wallet extends Model implements WalletInterface
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id'   => 'string',
        'user_type' => 'string',
        'name'      => 'string',
        'password'  => 'string',
        'guard'     => 'boolean',
        'overdraft' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'user_type',
        'name',
        'password',
        'guard',
        'overdraft',
    ];

    /**
     * @param string $password
     * @return Wallet
     */
    public function setPasswordAttribute(string $password): self
    {
        $this->attributes['password'] = Hash::make($password);

        return $this;
    }

    /**
     * @return MorphTo
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function balances(): HasMany
    {
        return $this->hasMany(WalletBalance::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasCurrency(string $name): bool
    {
        return $this->balances->contains('currency', $name);
    }

    /**
     * Get wallet currency.
     *
     * @param string|null $name
     * @return WalletCurrencyInterface
     */
    public function useCurrency(string $name = null): WalletCurrencyInterface
    {
        $name = $name ?? config('wallet.currency');

        return $this->balances->firstWhere('currency', $name);
    }

    /**
     * Check balance need password.
     *
     * @return bool
     */
    public function guardForAmount(): bool
    {
        return $this->getAttribute('guard');
    }

    /**
     * Overdraft the wallet.
     *
     * @return bool
     */
    public function overdraft(): bool
    {
        return $this->getAttribute('overdraft');
    }

    /**
     * Check wallet password is right.
     *
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->getAttribute('password'));
    }

    /**
     * Update wallet password.
     *
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(string $newPassword): bool
    {
        return $this->setAttribute('password', $newPassword)->save();
    }

    /**
     * Get wallet currency amount.
     *
     * @param string $currency
     * @return int
     */
    public function getCurrencyAmount(string $currency): int
    {
        return $this->useCurrency($currency)->amount();
    }

    /**
     * Deposit wallet currency.
     *
     * @param string $currency
     * @param int $amount
     * @return bool
     */
    public function depositCurrency(string $currency, int $amount): bool
    {
        return $this->useCurrency($currency)->deposit($amount);
    }

    /**
     * Withdraw wallet currency.
     *
     * @param string $currency
     * @param int $amount
     * @return bool
     */
    public function withdrawCurrency(string $currency, int $amount): bool
    {
        return $this->useCurrency($currency)->withdraw($amount);
    }
}
