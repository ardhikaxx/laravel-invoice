<?php

namespace Ardhikaxx\LaravelInvoice\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Laravel Invoice package by publishing configuration and migrations.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting installation of Laravel Invoice package...');

        $this->comment('Publishing configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'invoice-config']);

        $this->comment('Publishing migrations...');
        $this->callSilent('vendor:publish', ['--tag' => 'invoice-migrations']);
        
        $this->comment('Publishing templates/views (optional)...');
        $this->callSilent('vendor:publish', ['--tag' => 'invoice-views']);

        $this->info('Laravel Invoice installed successfully!');
        $this->info('Please run "php artisan migrate" to create the necessary tables.');
    }
}
