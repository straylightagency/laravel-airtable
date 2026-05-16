<?php

namespace Straylightagency\LaravelAirtable;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * ServiceProvider.
 *
 * @package Straylightagency\LaravelAirtable
 * @author Anthony Pauwels <anthony@straylightagency.be>
 */
class AirtableServiceProvider extends BaseServiceProvider
{
    /**
     * Register the DataLayer
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(AirtableManager::class, fn () => new AirtableManager(
            apiKey: config('airtable.api_key'),
            baseId: config('airtable.base_id'),
            apiUrl: config('airtable.api_url'),
        ) );
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        if ( $this->app->runningInConsole() ) {
            $this->publishes( [
                __DIR__ . '/config.php' => config_path('airtable.php'),
            ], 'airtable' );
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [ AirtableManager::class ];
    }
}
