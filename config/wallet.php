<?php

return [

    /**
     * |--------------------------------------------------------------------------
     * | Wallet Defaults
     * |--------------------------------------------------------------------------
     * |
     * | This option controls the default wallet & currency name and guard.
     * |
     */
    'defaults'   => [
        'wallet'   => 'wallet',
        'currency' => 'dollar',
        'guard'    => 'password',
    ],

    /**
     * |--------------------------------------------------------------------------
     * | Wallet Guards
     * |--------------------------------------------------------------------------
     * |
     * | Define every wallet guard for your application.
     * | Support driver: "hash", "verify-code"
     * |
     */
    'guards'     => [
        'password' => [
            'driver' => 'hash'
        ],
        'code'     => [
            'driver' => 'verify-code'
        ],
    ],

    /**
     * |--------------------------------------------------------------------------
     * | Currencies Configuration
     * |--------------------------------------------------------------------------
     * |
     * | Config currency scale.
     * |
     */
    'currencies' => [
        'dollar' => [
            'scale' => 2
        ],
    ],

];
