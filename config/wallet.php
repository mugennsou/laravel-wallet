<?php

return [

    /**
     * Default wallet name.
     */
    'default'     => 'wallet',

    /**
     * If wallet does exists, auto create it.
     */
    'auto_create' => true,

    /**
     * Guard for check balance.
     */
    'guard'       => false,

    /**
     * Overdraft the wallet.
     */
    'overdraft'   => false,

    /**
     * Default currency name.
     */
    'currency'    => 'dollar',

    /**
     * Currency scale.
     */
    'scale'       => [

        'dollar' => 2,

    ],

];
