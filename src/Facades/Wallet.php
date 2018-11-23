<?php

namespace Mugennsou\LaravelWallet\Facades;

use Illuminate\Support\Facades\Facade;

class Wallet extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'wallet';
    }
}
