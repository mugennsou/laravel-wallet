<?php

namespace Mugennsou\LaravelWallet\Exceptions;

class RichnessErrorException extends Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct($message ?: 'Must set richness first.');
    }
}
