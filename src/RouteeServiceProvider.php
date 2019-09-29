<?php

namespace NotificationChannels\Routee;

use Illuminate\Support\ServiceProvider;

class RouteeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->singleton(RouteeApi::class, function ($app) {
            $config = config("services.routee");
            return new RouteeApi(
                $config["app_id"],
                $config["secret"],
                $config["from"]
            );
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
