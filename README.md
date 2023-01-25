# Laravel Authentication Package

This package will provide APIs for user authentication that is registration and login APIs with routes.

## Installation

In order to install the package use the command specified below - 

```bash
composer require arhamlabs/authenticator

```

## Configuration

Get inside the **config/app.php** file then add socialite services in providers

```bash
'providers' => [
    ....
    .... 
    Arhamlabs\Authentication\AuthenticationServiceProvider::class

],

```
The defaults configuration settings are set in config/al_auth_config.php. Copy this file to your own config directory to modify the values or you can publish the config using this command:

```bash

php artisan vendor:publish --provider="Arhamlabs\Authentication\AuthenticationServiceProvider"

```
Finally, you should run your database migrations. This package will create following tables into database:

1.temp_registrations 
2.auth_settings
3.temp_otp

Also for mobile otp authentication one more migration is used.Which will add columns into the user table.

**Command:**

```bash
php artisan migrate

```


**Sanctum Token Ability Middleware Setup:**

Sanctum also includes two middleware that may be used to verify that an incoming request is authenticated with a token that has been granted a given ability. To get started, add the following middleware to the $routeMiddleware property of your application's app/Http/Kernel.php file:

```bash
'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class

```


**Sanctum Token authentication exception handling on route:**

To handle default exception on api routes such as AuthenticationException/AccessDeniedHttpException update the renderable function to the register() of your application's app/Exception/Handler.php file:

```bash

<?php

namespace App\Exceptions;

use Arhamlabs\ApiResponse\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        $this->renderable(function (AuthenticationException $e, $request) {
            $errorResponse = new ApiResponse;
            if ($request->is('api/*')) {
                $customUserMessageTitle = 'Sorry, we were unable to authenticate your request';
                $errorResponse->setCustomResponse($customUserMessageTitle);
                return $errorResponse->getResponse(401, []);
            }
        });

        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            $errorResponse = new ApiResponse;
            if ($request->is('api/*')) {
                return $errorResponse->getResponse(403, []);
            }
        });
        $this->renderable(function (NotFoundHttpException $e, $request) {
            $errorResponse = new ApiResponse;
            if ($request->is('api/*')) {
                return $errorResponse->getResponse(404, []);
            }
        });
    }
}


```