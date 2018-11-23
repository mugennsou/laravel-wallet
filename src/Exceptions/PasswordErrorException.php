<?php

namespace Mugennsou\LaravelWallet\Exceptions;

class PasswordErrorException extends Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct($message ?: 'Wallet password error.');
    }
}
