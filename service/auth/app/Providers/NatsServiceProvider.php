<?php

namespace App\Providers;

use App\Infrastructure\Nats\Nats;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class NatsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Nats::class, function ($app) {
            $host    = getenv('NATS_HOST');
            $port    = getenv('NATS_PORT');

            return new Nats($host, $port);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Nats::class];
    }
}
