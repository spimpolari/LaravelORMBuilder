<?php

namespace spimpolari\LaravelORMBuilder;

use Illuminate\Support\ServiceProvider;

class ORMBuilderServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The console commands.
     *
     * @var bool
     */
    protected $commands = [
        'spimpolari\LaravelORMBuilder\ORMBuilderTableCommand',
        'spimpolari\LaravelORMBuilder\ORMBuilderDBCommand',
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Not really anything to boot.
    }

    /**
     * Register the command.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['orm'];
    }
}