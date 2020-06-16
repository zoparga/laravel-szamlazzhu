<?php


namespace SzuniSoft\SzamlazzHu\Providers;


use Illuminate\Support\ServiceProvider;
use SzuniSoft\SzamlazzHu\Client\Client;

class SzamlazzHuServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Register provider
     */
    public function register()
    {

        // Load config
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'szamlazz-hu');

        // Register the API client
        $this->registerApiClient();
    }

    /**
     * Register API client
     */
    protected function registerApiClient()
    {

        $this->app->bind(Client::class, function ($app) {
            return new Client(
                $app['config']['szamlazz-hu']['client'],
                new \GuzzleHttp\Client(),
                $app['config']['szamlazz-hu']['merchant']
            );
        });
        $this->app->alias(Client::class, 'szamlazz-hu.client');

    }

    /**
     * Boot service provider
     */
    public function boot()
    {

        // Publish config
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('szamlazz-hu.php'),
        ], 'config');
    }

    /**
     * Provider is defer
     *
     * @return array
     */
    public function provides()
    {
        return [
            Client::class,
            'szamlazz-hu.client',
        ];
    }


}
