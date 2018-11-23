<?php

namespace Mugennsou\LaravelWallet\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeCurrency extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create currency for the wallet.';

    /**
     * Create a new queue job table command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['currency', InputArgument::REQUIRED, 'The new currency name.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['total', null, InputOption::VALUE_OPTIONAL, 'The currency total places.'],
            ['scale', null, InputOption::VALUE_OPTIONAL, 'The currency scale places.'],
        ];
    }
}
