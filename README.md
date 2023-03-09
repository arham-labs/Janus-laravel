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
The defaults configuration settings are set in the **config/al_auth_config.php** file. Copy this file to your own config directory to modify the values or you can publish the config using this command:

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

Sanctum also includes two middleware that may be used to verify that an incoming request is authenticated with a token that has been granted a given ability. To get started, add the following middleware to the $routeMiddleware property of your application's **app/Http/Kernel.php** file:

```bash
'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class

```


**Sanctum Token authentication exception handling on route:**

To handle default exception on api routes such as AuthenticationException/AccessDeniedHttpException add following code into the register function of your application's **app/Exception/Handler.php** file:

```bash

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


```


## API Reference 

## *Registration*

#### User registration

- Users can register using email and password.Data will be saved in a temporary table.
- Next If the config flag email_verification=false then details will be saved in the main table i.e users table as well as user_setting table will be updated. 
- If the config set to email_verification=true then verification mail will send to users email.

```bash
  POST /api/package/auth/register

```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `first_name` | `string` ||
| `last_name` | `string` ||
| `user_type` | `string` ||
| `username` | `string` ||
| `email`    | `string` | *Required*|
| `password` | `string` |*Required*|
| `mobile`   | `number` ||  
| `country_code` | `number` ||

---


# *User Login*

### User Login using Username and Password
- Users can login with Email/Mobile/Username and password.
- Once user gets authenticated then laravel sanctum token will be generated.


```bash
  POST /api/package/auth/login
```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `username`    | `string` | *Required*|
| `password` | `string` |*Required*|



---

### User Login with Email and OTP
- Users can login using email and otp. OTP will be sent to the user via email. 
- Once otp send temp_otp table will be used for maintaining the verification details.



```bash
  POST /api/package/auth/sent-email-otp

```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `email`    | `string` | *Required*|



---

### OTP Verification
- This api is used to verify OTP. 
- Once OTP gets verified a token will be generated. Number of attempts will be added to the function.



```bash
  POST /api/package/auth/verify-otp
```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `otp`    | `number` | *Required*|
| `email`    | `string` | *Required*|



---

### User Login with Mobile and OTP
- Users can login using sms and otp. OTP will be sent to the user via sms. 
- Once otp send temp_otp table will be used for maintaining the verification details.



```bash
  POST /api/package/auth/sent-mobile-otp
```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `mobile`    | `number` | *Required*|
| `country_code`    | `number` | *Required*|



---


### OTP Verification
- This api is used to verify OTP. 
- Once OTP gets verified a token will be generated. Number of attempts will be added to the function.



```bash
  POST /api/package/auth/verify-otp
```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `otp`    | `number` | *Required*|
| `mobile`    | `number` | *Required*|
| `country_code`    | `number` | *Required*|



---

### Logout
- This api is used to logout user and it will destroy sanctum token of that user.



```bash
  POST /api/package/auth/logout

```

