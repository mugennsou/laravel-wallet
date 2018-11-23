<?php

namespace Mugennsou\LaravelWallet;

use Illuminate\Database\Eloquent\Model;
use Kra8\Snowflake\Snowflake;

class WalletTransaction extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'               => 'integer',
        'transaction_id'   => 'string',
        'wallet_id'        => 'integer',
        'balance_id'       => 'integer',
        'type'             => 'string',
        'currency'         => 'string',
        'amount'           => 'integer',
        'available_amount' => 'integer',
        'accepted'         => 'boolean',
        'meta'             => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'currency',
        'amount',
        'available_amount',
        'accepted',
        'meta',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (WalletTransaction $transaction) {
            $transaction->setAttribute('transaction_id', app(Snowflake::class)->next());
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function balance()
    {
        return $this->belongsTo(WalletBalance::class);
    }
}
