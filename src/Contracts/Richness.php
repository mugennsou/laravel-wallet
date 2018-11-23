<?php

namespace Mugennsou\LaravelWallet\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Richness
{
    public function wallets(): MorphMany;

    public function hasWallet(string $name): bool;

    public function wallet(string $name): WalletInterface;
}
