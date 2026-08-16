<?php

namespace Ardhikaxx\LaravelInvoice;

use Illuminate\Support\ServiceProvider;
use Ardhikaxx\LaravelInvoice\Contracts\PdfGeneratorInterface;
use Ardhikaxx\LaravelInvoice\Services\Pdf\DompdfGenerator;

class InvoiceServiceProvider extends ServiceProvider
{
    /**
        * Register any application services.
        */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/invoice.php', 'invoice'
        );

        $this->app->singleton('invoice', function ($app) {
            return new InvoiceManager($app);
        });
        
        $this->app->bind(PdfGeneratorInterface::class, function ($app) {
            // Can be resolved based on config('invoice.pdf.driver')
            return new DompdfGenerator();
        });
    }

    /**
        * Bootstrap any application services.
        */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/invoice.php' => config_path('invoice.php'),
            ], 'invoice-config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], 'invoice-migrations');
            
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/invoice'),
            ], 'invoice-views');

            $this->commands([
                Commands\InstallCommand::class,
            ]);
        }
        
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'invoice');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'invoice');
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
    }
}
