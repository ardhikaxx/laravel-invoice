<?php

namespace Ardhikaxx\LaravelInvoice\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Ardhikaxx\LaravelInvoice\InvoiceServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [
            InvoiceServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        
        // Ensure decimal precision is set for tests
        $app['config']->set('invoice.decimal_precision', 2);
    }
    
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
