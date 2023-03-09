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
            ], 'al_auth_config');

            $this->publishes([
                __DIR__ . '/config/al_auth_validation_config.php' => config_path('al_auth_validation_config.php'),
            ], 'al_auth_validation_config');

            $this->publishes([
                __DIR__ . '/resources/lang/en/messages.php' => resource_path('lang/en/messages.php'),
            ], 'locale_messages');

            $this->publishes([
                __DIR__ . '/resources/lang/en/error_messages.php' => resource_path('lang/en/error_messages.php'),
            ], 'locale_error_messages');

            $this->publishes([
                __DIR__ . '/resources/lang/en/validation_messages.php' => resource_path('lang/en/validation_messages.php'),
            ], 'locale_validation_messages');

            $this->publishes([
                __DIR__ . '/resources/views/mails' => resource_path('views/mails'),
            ], 'mails');
        }
    }
}
