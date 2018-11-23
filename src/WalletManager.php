<?php

namespace Mugennsou\LaravelWallet;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Manager;
use Mugennsou\LaravelWallet\Contracts\Richness;
use Mugennsou\LaravelWallet\Contracts\WalletCurrencyInterface;
use Mugennsou\LaravelWallet\Contracts\WalletInterface;
use Mugennsou\LaravelWallet\Exceptions\BalanceNotEnoughException;
use Mugennsou\LaravelWallet\Exceptions\Exception;
use Mugennsou\LaravelWallet\Exceptions\PasswordErrorException;

/**
 * Class WalletManager
 * @package Mugennsou\LaravelWallet
 * @method WalletInterface|Wallet driver($driver = null)
 */
class WalletManager extends Manager implements WalletInterface, WalletCurrencyInterface
{
    /**
     * @var WalletCurrencyInterface[]|WalletBalance[]
     */
    protected $currencies = [];

    /**
     * @var Richness
     */
    protected $richness;

    /**
     * @var string
     */
    protected $wallet;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var bool
     */
    protected $checked = false;

    /**
     * @param Richness $richness
     * @param string $password
     * @param string|null $wallet
     * @param string|null $currency
     * @return $this
     */
    public function for(Richness $richness, string $password = '', string $wallet = null, string $currency = null): self
    {
        $this->richness($richness);
        $this->wallet   = $wallet;
        $this->currency = $currency;
        $this->checked  = $this->checkPassword($password);

        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function password(string $password): self
    {
        $this->checked = $this->checkPassword($password);

        return $this;
    }

    /**
     * Skip password check.
     *
     * @param bool $skip
     * @return $this
     */
    public function skipPassword($skip = true): self
    {
        $this->checked = $skip;

        return $this;
    }

    /**
     * @param string $wallet
     * @return $this
     */
    public function wallet(string $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function currency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Create wallet.
     *
     * @param string $name
     * @param string $password
     * @return \Illuminate\Database\Eloquent\Model|WalletInterface|Wallet
     */
    public function createWallet(string $name, string $password = ''): WalletInterface
    {
        return $this->richness->wallets()->create([
            'name'      => $name,
            'passport'  => $password,
            'guard'     => $this->app->make('config')->get('wallet.guard', false),
            'overdraft' => $this->app->make('config')->get('wallet.overdraft', false),
        ]);
    }

    /**
     * Create wallet.
     *
     * @param string $name
     * @param int $amount
     * @return \Illuminate\Database\Eloquent\Model|WalletCurrencyInterface|WalletBalance
     */
    public function createCurrencyBalance(string $name, int $amount = 0): WalletCurrencyInterface
    {
        return $this->driver()->balances()->create([
            'currency' => $name,
            'amount'   => $amount,
        ]);
    }

    /**
     * @param int $amount
     * @param string $type
     * @param bool $accepted
     * @param array $meta
     * @return bool
     * @throws \Throwable
     */
    public function createTransaction(int $amount, string $type, bool $accepted, array $meta = []): bool
    {
        throw_if($amount < 0, new Exception('Transaction amount must greater than 0'));

        $transaction = new WalletTransaction([
            'type'             => $type,
            'currency'         => $this->getDefaultCurrency(),
            'amount'           => $amount,
            'available_amount' => $this->getAmount(),
            'accepted'         => $accepted,
            'meta'             => $meta,
        ]);

        $transaction->wallet()->associate($this->driver());
        $transaction->balance()->associate($this->useCurrency());

        return $transaction->save();
    }

    /**
     * Get wallet.
     *
     * @param string|null $wallet
     * @return WalletInterface|Wallet
     */
    public function useWallet(string $wallet = null): WalletInterface
    {
        return $this->driver($wallet);
    }

    /**
     * Check wallet currency exists.
     *
     * @param string $name
     * @return bool
     */
    public function hasCurrency(string $name): bool
    {
        return $this->driver()->hasCurrency($name);
    }

    /**
     * Get wallet currency.
     *
     * @param string|null $name
     * @return WalletCurrencyInterface
     */
    public function useCurrency(string $name = null): WalletCurrencyInterface
    {
        $name = $name ?? $this->getDefaultCurrency();

        if (!isset($this->currencies[$name])) {
            $this->currencies[$name] = $this->createCurrency($name);
        }

        return $this->currencies[$name];
    }

    /**
     * Check balance need password.
     *
     * @return bool
     */
    public function guardForAmount(): bool
    {
        return $this->driver()->guardForAmount();
    }

    /**
     * Overdraft the wallet.
     *
     * @return bool
     */
    public function overdraft(): bool
    {
        return $this->driver()->overdraft();
    }

    /**
     * Check wallet password is right.
     *
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        return $this->driver()->checkPassword($password);
    }

    /**
     * Update wallet password.
     *
     * @param string $newPassword
     * @param string $oldPassword
     * @return bool
     * @throws \Throwable|PasswordErrorException
     */
    public function updatePassword(string $newPassword, string $oldPassword = ''): bool
    {
        throw_unless($this->passwordChecked() || $this->checkPassword($oldPassword), PasswordErrorException::class);

        return $this->driver()->updatePassword($newPassword);
    }

    /**
     * Get wallet default currency amount.
     *
     * @return int
     * @throws \Throwable|PasswordErrorException
     */
    public function amount(): int
    {
        throw_if($this->guardForAmount() && !$this->passwordChecked(), PasswordErrorException::class);

        return $this->useCurrency()->amount();
    }

    /**
     * @param int $amount
     * @param string $type
     * @param bool $accepted
     * @param array $meta
     * @return bool
     * @throws \Throwable|Exception
     */
    public function deposit(int $amount, string $type = 'deposit', bool $accepted = true, array $meta = []): bool
    {
        return $this->depositWithTransaction($amount, $type, $meta, $accepted);
    }

    /**
     * Deposit wallet currency without transaction.
     *
     * @param int $amount
     * @return bool
     * @throws \Throwable
     */
    public function depositWithoutTransaction(int $amount): bool
    {
        throw_if($amount < 0, new Exception('Deposit amount must greater than 0.'));

        return $this->useCurrency()->deposit($amount);
    }

    /**
     * Withdraw wallet currency.
     *
     * @param int $amount
     * @param string $type
     * @param bool $accepted
     * @param array $meta
     * @return bool
     * @throws \Throwable|PasswordErrorException|BalanceNotEnoughException|Exception
     */
    public function withdraw(int $amount, string $type = 'withdraw', bool $accepted = true, array $meta = []): bool
    {
        return $this->withdrawWithTransaction($amount, $type, $meta, $accepted);
    }

    /**
     * @param int $amount
     * @return bool
     * @throws \Throwable|PasswordErrorException|BalanceNotEnoughException
     */
    public function withdrawWithoutTransaction(int $amount)
    {
        throw_if($amount < 0, new Exception('Withdraw amount must greater than 0.'));
        throw_unless($this->passwordChecked(), PasswordErrorException::class);
        throw_unless($this->canWithdraw($amount), BalanceNotEnoughException::class);

        return $this->useCurrency()->withdraw($amount);
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
        return $this->overdraft() || $this->amount() >= $amount;
    }

    /**
     * Determine if password was checked.
     *
     * @return bool
     */
    public function passwordChecked(): bool
    {
        return $this->checked;
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->wallet ?? $this->app->make('config')->get('wallet.default', 'wallet');
    }

    /**
     * Get the default wallet currency name.
     *
     * @return string
     */
    public function getDefaultCurrency(): string
    {
        return $this->currency ?? $this->app->make('config')->get('wallet.currency', 'dollar');
    }

    /**
     * @param Richness $richness
     */
    protected function richness(Richness $richness)
    {
        $this->richness   = $richness;
        $this->drivers    = [];
        $this->currencies = [];
    }

    /**
     * Skip password check to get balance.
     *
     * @return int
     */
    protected function getAmount(): int
    {
        return $this->useCurrency()->amount();
    }

    /**
     * @param int $amount
     * @param string $type
     * @param array $meta
     * @param bool $accepted
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    protected function depositWithTransaction(int $amount, string $type = 'deposit', array $meta = [], bool $accepted = true): bool
    {
        $results = [];
        DB::beginTransaction();

        try {
            $results[] = $this->depositWithoutTransaction($amount);
            $results[] = $this->createTransaction($amount, $type, $accepted, $meta);

            if (in_array(false, $results))
                throw new Exception(ucfirst($type) . ' failed.');
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();
        return true;
    }

    /**
     * @param int $amount
     * @param string $type
     * @param array $meta
     * @param bool $accepted
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    protected function withdrawWithTransaction(int $amount, string $type = 'withdraw', array $meta = [], bool $accepted = true): bool
    {
        $results = [];
        DB::beginTransaction();

        try {
            $results[] = $this->withdrawWithoutTransaction($amount);
            $results[] = $this->createTransaction($amount, $type, $accepted, $meta);

            if (in_array(false, $results))
                throw new Exception(ucfirst($type) . ' failed.');
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();
        return true;
    }

    /**
     * Create a new wallet.
     *
     * @param string $driver
     * @return WalletInterface
     * @throws RichnessErrorException
     */
    protected function createDriver($driver)
    {
        if (is_null($this->richness))
            throw new RichnessErrorException();

        if (
            !$this->richness->hasWallet($driver)
            && $this->app->make('config')->get('wallet.auto_create', false)
        ) {
            $this->createWallet($driver);
            $this->richness->load('wallets');
        }

        return $this->richness->wallet($driver);
    }

    /**
     * Create a new currency.
     *
     * @param string $currency
     * @return WalletCurrencyInterface
     */
    protected function createCurrency(string $currency)
    {
        if (
            !$this->hasCurrency($currency)
            && $this->app->make('config')->get('wallet.auto_create', false)
        ) {
            $this->createCurrencyBalance($currency);
            $this->driver()->load('balances');
        }

        return $this->driver()->useCurrency($currency);
    }
}
