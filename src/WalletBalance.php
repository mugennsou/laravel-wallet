<?php

namespace Mugennsou\LaravelWallet;

use Illuminate\Database\Eloquent\Model;
use Mugennsou\LaravelWallet\Contracts\WalletCurrencyInterface;

class WalletBalance extends Model implements WalletCurrencyInterface
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'        => 'integer',
        'wallet_id' => 'string',
        'currency'  => 'string',
        'amount'    => 'integer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'currency',
        'amount',
    ];

    /**
     * Get calc amount
     *
     * @param int $amount
     * @return float
     */
    public function getAmountAttribute(int $amount): float
    {
        $scale = config('wallet.scale.' . $this->getAttribute('currency'), 0);
        $pow   = bcpow(10, (string)$scale);
        return (float)bcdiv((string)$amount, $pow, $scale);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get currency amount.
     *
     * @return int
     */
    public function amount(): int
    {
        return (int)$this->getAttribute('amount');
    }

    /**
     * Deposit currency.
     *
     * @param int $amount
     * @return bool
     */
    public function deposit(int $amount): bool
    {
        $newAmount = bcadd((string)$this->amount(), (string)$amount);

        return $this->setAttribute('amount', (int)$newAmount)->save();
    }

    /**
     * Withdraw currency.
     *
     * @param int $amount
     * @return bool
     */
    public function withdraw(int $amount): bool
    {
        $newAmount = bcsub((string)$this->amount(), (string)$amount);

        return $this->setAttribute('amount', (int)$newAmount)->save();
    }
}
