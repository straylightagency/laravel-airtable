<?php

namespace Straylightagency\LaravelAirTable;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * ServiceProvider.
 *
 * @package Straylightagency\LaravelAirTable
 * @author Anthony Pauwels <anthony@straylightagency.be>
 */
class AirTableServiceProvider extends BaseServiceProvider
{
    /**
     * Register the DataLayer
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('airtable', fn () => new AirTableManager(
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
            ] );
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [ AirTableManager::class ];
    }
}
