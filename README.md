# Laravel Authentication Package

This package will provide APIs for user authentication that is registration and login APIs with routes.

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


**Token Ability Middleware:**

Sanctum also includes two middleware that may be used to verify that an incoming request is authenticated with a token that has been granted a given ability. To get started, add the following middleware to the $routeMiddleware property of your application's app/Http/Kernel.php file:

```bash
'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class

```