<?php

namespace Arhamlabs\Authentication;

use Arhamlabs\Authentication\Interfaces\AuthLoginALInterface;
use Arhamlabs\Authentication\Interfaces\AuthRegistrationALInterface;
use Arhamlabs\Authentication\Repositories\AuthLoginALRepository;
use Arhamlabs\Authentication\Repositories\AuthRegistrationALRepository;
use Illuminate\Support\ServiceProvider;

class AuthenticationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AuthRegistrationALInterface::class, AuthRegistrationALRepository::class);
        $this->app->bind(AuthLoginALInterface::class, AuthLoginALRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        if (app()->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/database/migrations' => database_path('migrations'),
            ], 'migrations');

            $this->publishes([
                __DIR__ . '/config/al_auth_config.php' => config_path('al_auth_config.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/resources/lang/en/messages.php' => resource_path('lang/en/messages.php'),
            ], 'locale');
            $this->publishes([
                __DIR__ . '/resources/lang/en/error_messages.php' => resource_path('lang/en/error_messages.php'),
            ], 'locale');

            $this->publishes([
                __DIR__ . '/resources/views/mails' => resource_path('views/mails'),
            ], 'mails');
        }
    }
}
