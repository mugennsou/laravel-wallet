<?php

namespace Mugennsou\LaravelWallet\Exceptions;

class BalanceNotEnoughException extends Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct($message ?: 'Balance not enough.');
    }
}
